<?php

// src/AppBundle/Service/Email/UserMailer.php

namespace AppBundle\Service\Email;

use AppBundle\Entity\User;

/**
 * Sending an email to a user.
 */
class UserMailer
{
    const EMAIL_SET_FROM = 'registration@snowtricks.com';

    /**
     * An instance of Swift_Mailer.
     *
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * An instance of the Twig_Environment.
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * Send an email for any new registration.
     *
     * @param User $user
     *
     * @return bool
     */
    public function sendNewRegistration(User $user): bool
    {
        $template = $this->twig->loadTemplate('Email/registration.twig');

        $this->subject = $template->renderBlock('subject', []);
        $this->bodyHtml = $template->renderBlock('body_html', ['token' => $user->getToken()]);
        $this->bodyText = $template->renderBlock('body_text', ['token' => $user->getToken()]);

        // 2) Create the email message
        $message = $this->constructMessage();
        $message->setTo($user->getEmail());

        // 3) Send the email
        return $this->mailer->send($message);
    }

    /**
     * Sending an email when a user has lost it.
     *
     * @param User $user
     *
     * @return bool
     */
    public function sendForgotPassword(User $user): bool
    {
        $template = $this->twig->loadTemplate('Email/reset_password.twig');

        $this->subject = $template->renderBlock('subject', []);
        $this->bodyHtml = $template->renderBlock('body_html', ['token' => $user->getToken()]);
        $this->bodyText = $template->renderBlock('body_text', ['token' => $user->getToken()]);

        // 2) Create the email message
        $message = $this->constructMessage();
        $message->setTo($user->getEmail());

        // 3) Send the email
        return $this->mailer->send($message);
    }

    /**
     * Construct the mail.
     */
    public function constructMessage()
    {
        return (new \Swift_Message())
            // Give the message a subject
            ->setSubject($this->subject)
            ->setBody($this->bodyText, 'text/plain')
            // And optionally an alternative body
            ->addPart($this->bodyHtml, 'text/html')
            ->setFrom(self::EMAIL_SET_FROM)
        ;
    }
}
