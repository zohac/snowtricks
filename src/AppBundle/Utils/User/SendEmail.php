<?php

// src/AppBundle/Service/User/Registration.php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Register a new user.
 */
class SendEmail
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param SessionInterface $session
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, SessionInterface $session)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->flashBag = $session->getFlashBag();
    }


    /**
     * User registration.
     *
     * @param User $user
     */
    public function sendForValidation(User $user)
    {
        $template = $this->twig->load('Email/registration.twig');
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

        // Send an email
        if ($this->mailer->send($mail)) {
            $type = 'info';
            $message = 'Vérifiez votre boîte mail, pour confirmer votre nouvel email.';
        }
        // Add flash message
        $this->flashBag->add($type, $message);
    }
}
