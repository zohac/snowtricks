<?php

namespace AppBundle\Utils\User;

use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserTypeHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

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
     * @param ObjectManager                $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SessionInterface             $session
     * @param \Twig_Environment            $twig
     * @param \Swift_Mailer                $mailer
     */
    public function __construct(
        ObjectManager $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SessionInterface $session,
        \Twig_Environment $twig,
        \Swift_Mailer $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->flashBag = $session->getFlashBag();
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handle(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // 1) Get the original user
            $originalUser = $this->entityManager->getUnitOfWork()->getOriginalEntityData($user);

            // 3) If the email has changed
            if ($originalUser['email'] != $user->getEmail()) {
                // 3.1) Set a token for registration and change the role
                $user->setRoles([]);
                $user->setToken(hash('sha256', serialize($user).microtime()));

                // 3.2) Send an email
                $template = $this->twig->load('Email/change_email.twig');
                $mail = (new \Swift_Message())
                    // Give the message a subject
                    ->setSubject($template->renderBlock('subject', []))
                    ->setBody($template->renderBlock('body_text', ['token' => $user->getToken()]), 'text/plain')
                    // And optionally an alternative body
                    ->addPart($template->renderBlock('body_html', ['token' => $user->getToken()]), 'text/html')
                    ->setTo($user->getEmail())
                    ->setFrom('contact@snowtricks.com')
                ;

                // Initialisation the flashbag message.
                $type = 'danger';
                $message = 'Un email de confirmation n\'a pu vous être envoyé.
                    Connectez vous à votre compte et vérifié votre adresse mail. 
                    Tant que votre adresse email ne seras pas vérifié,
                    vous ne pourrez pas poster de Trick ou des commentaires.';

                if ($this->mailer->send($mail)) {
                    $type = 'info';
                    $message = 'Vérifiez votre boîte mail, pour confirmer votre nouvel email.';
                }
                // 8) In case of error
                $this->flashBag->add($type, $message);
            }

            // 4) Encode the password
            if ($user->getPlainPassword()) {
                $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }

            // 5) save the user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
