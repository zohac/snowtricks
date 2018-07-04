<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Events\ResetPasswordEvent;
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
