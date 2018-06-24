<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use AppBundle\Events\ResetPasswordEvent;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param SessionInterface  $session
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer     $mailer
     */
    public function __construct(
        SessionInterface $session,
        \Twig_Environment $twig,
        \Swift_Mailer $mailer
    ) {
        $this->flashBag = $session->getFlashBag();
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    /**
     * Register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        // We're getting the User.
        $entity = $args->getEntity();

        // only act on some "User" entity
        if (!$entity instanceof User) {
            return;
        }

        // 1) Set a token for registration
        $entity->setToken(hash('sha256', serialize($entity).microtime()));
        // 2) Set Role
        $entity->setRoles([]);
        // 3) Set date of the registration.
        $entity->setDateRegistration(new \Datetime('NOW'));

        $avatar = new Picture();
        $avatar->setName('user.svg');
        $avatar->setPath(Picture::DEFAULT_USER);
        $entity->setAvatar($avatar);
    }

    /**
     * Send a mail after register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // 1) We're getting the User.
        $entity = $args->getEntity();

        // 2) only act on some "User" entity
        if (!$entity instanceof User) {
            return;
        }

        // 3) Send a confirmation mail
        $template = $this->twig->load('Email/registration.twig');
        $mail = (new \Swift_Message())
            // Give the message a subject
            ->setSubject($template->renderBlock('subject', []))
            ->setBody($template->renderBlock('body_text', ['token' => $entity->getToken()]), 'text/plain')
            // And optionally an alternative body
            ->addPart($template->renderBlock('body_html', ['token' => $entity->getToken()]), 'text/html')
            ->setTo($entity->getEmail())
            ->setFrom('contact@snowtricks.com')
        ;

        if (!$this->mailer->send($mail)) {
            $this->flashBag->add(
                'danger',
                'Un email de confirmation n\'a pu vous être envoyé.
                Connectez vous à votre compte et vérifié votre adresse mail. 
                Tant que votre adresse email ne seras pas vérifié,
                vous ne pourrez pas poster de Trick ou des commentaires.'
            );
        }
    }

    /**
     * Update a User.
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs  $args)
    {
        // 1) We're getting the User.
        $entity = $args->getEntity();

        // 2) only act on some "User" entity
        if (!$entity instanceof User) {
            return;
        }

        // 3) If the email has changed
        if ($args->hasChangedField('email')) {
            // 4) Set a token for registration and change the role
            $entity->setRoles([]);
            $entity->setToken(hash('sha256', serialize($entity).microtime()));

            // 5) Send an email
            $template = $this->twig->load('Email/change_email.twig');
            $mail = (new \Swift_Message())
                // Give the message a subject
                ->setSubject($template->renderBlock('subject', []))
                ->setBody($template->renderBlock('body_text', ['token' => $entity->getToken()]), 'text/plain')
                // And optionally an alternative body
                ->addPart($template->renderBlock('body_html', ['token' => $entity->getToken()]), 'text/html')
                ->setTo($entity->getEmail())
                ->setFrom('contact@snowtricks.com')
            ;

            if ($this->mailer->send($mail)) {
                $this->flashBag->add(
                    'info',
                    'Vérifiez votre boîte mail, pour confirmer votre nouvel email.'
                );

                return;
            }
            // 8) In case of error
            $this->flashBag->add(
                'danger',
                'Un email de confirmation n\'a pu vous être envoyé.
                Connectez vous à votre compte et vérifié votre adresse mail. 
                Tant que votre adresse email ne seras pas vérifié,
                vous ne pourrez pas poster de Trick ou des commentaires.'
            );
        }
    }

    /**
     * Undocumented function.
     *
     * @param ResetPasswordEvent $event
     */
    public function onResetPassword(ResetPasswordEvent $event)
    {
        // 1) We're getting the User.
        $user = $event->getUser();
        $user->setToken(hash('sha256', serialize($user).microtime()));

        // 2) Send an email
        $template = $this->twig->load('Email/reset_password.twig');
        $mail = (new \Swift_Message())
            // Give the message a subject
            ->setSubject($template->renderBlock('subject', []))
            ->setBody($template->renderBlock('body_text', ['token' => $user->getToken()]), 'text/plain')
            // And optionally an alternative body
            ->addPart($template->renderBlock('body_html', ['token' => $user->getToken()]), 'text/html')
            ->setTo($user->getEmail())
            ->setFrom('contact@snowtricks.com')
        ;

        if (!$this->mailer->send($mail)) {
            $this->flashBag->add(
                'danger',
                'Un email de confirmation n\'a pu vous être envoyé.
                Connectez vous à votre compte et vérifié votre adresse mail.
                Tant que votre adresse email ne seras pas vérifié,
                vous ne pourrez pas poster de Trick ou des commentaires.'
            );
        }
    }
}
