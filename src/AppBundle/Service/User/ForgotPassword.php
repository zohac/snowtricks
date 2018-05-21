<?php

// src/AppBundle/Service/User/ForgotPassword.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormView;
use AppBundle\Service\Email\UserMailer;
use AppBundle\Form\User\ForgotPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param UserMailer           $mailer
     * @param ObjectManager        $entityManager
     * @param SessionInterface     $session
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        UserMailer $mailer,
        ObjectManager $entityManager,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->formFactory = $formFactory;
    }

    /**
     * Send an email to reset the password.
     *
     * @param User $user
     */
    public function sendToken(Request $request, ?User $user): ?FormView
    {
        // If the user doesn't exist
        if (!$user) {
            throw new \LogicException(
                sprintf('L\'utilisateur n\'existe pas! Avez vous bien suivi le lien envoyé par email!')
            );
        }

        // Build the form
        $form = $this->formFactory->create(ForgotPasswordType::class, $user);

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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

                return null;
            }
            $this->flashBag->add(
                'forgot_password_email_send_error',
                [
                    'type' => 'danger',
                    'title' => 'Une erreur c\'est produit lors de l\'envoie du mail!',
                    'message' => 'Nous n\'avons pu vous envoyer un lien pour récupérer votre mot de passe.',
                ]
            );

            return null;
        }

        return $form->createView();
    }
}
