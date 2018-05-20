<?php

// src/AppBundle/Service/User/ForgotPassword.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Service\Email\UserMailer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class managing lost passwords.
 */
class ForgotPassword
{
    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * Constructor.
     *
     * @param UserMailer           $mailer
     * @param ObjectManager        $entityManager
     * @param SessionInterface     $session
     * @param RequestStack         $requestStack
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        UserMailer $mailer,
        ObjectManager $entityManager,
        SessionInterface $session
    ) {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Send an email to reset the password.
     *
     * @param User $user
     */
    public function forgotPassword(User $user)
    {
        // If the user doesn't exist
        if (empty($user)) {
            $this->flashBag->add(
                'forgot_password',
                [
                    'type' => 'danger',
                    'title' => 'Aucun utilisateur connu avec cette adresse email!',
                    'message' => 'Voulez-vous vous inscrire?',
                ]
            );

            return;
        }

        // 3) Set a token for ressetting password
        $user->setToken(hash('sha256', serialize($user).microtime()));

        // 5) save the User!
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 6) Sending an email
        if ($this->mailer->sendForgotPassword($user)) {
            $this->flashBag->add(
                'forgot_password_email_send_success',
                [
                    'type' => 'info',
                    'title' => 'Nous vous avons envoyé un e-mail pour réinitialiser votre mot de passe.',
                    'message' => '',
                ]
            );

            return;
        }
        $this->flashBag->add(
            'forgot_password_email_send_error',
            [
                'type' => 'danger',
                'title' => 'Une erreur c\'est produit lors de l\'envoie du mail!',
                'message' => 'Nous n\'avons pu vous envoyer un lien pour récupérer votre mot de passe.',
            ]
        );
    }
}
