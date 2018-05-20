<?php

// src/AppBundle/Service/User/Registration.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Service\Email\UserMailer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Register a new user.
 */
class Registration
{
    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * @var RegistrationMailer
     */
    private $passwordEncoder;

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
     * @param UserMailer                   $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ObjectManager                $entityManager
     * @param SessionInterface             $session
     */
    public function __construct(
        UserMailer $mailer,
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $entityManager,
        SessionInterface $session
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Register a new user.
     *
     * @param User $user
     */
    public function registration(User $user)
    {
        // 1) Encode the password
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        // 2) Set a token for registration
        $user->setToken(hash('sha256', serialize($user).microtime()));
        $user->setRoles([]);

        // 3) save the User!
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 4) Send a confirmation mail
        if ($this->mailer->sendNewRegistration($user)) {
            $this->flashBag->add(
                'update_user',
                [
                   'type' => 'info',
                   'title' => 'Vérifiez votre email, pour confirmer votre inscription.',
                   'message' => '',
                ]
            );

            return;
        }
        // 5) In case of error
        $this->flashBag->add(
            'update_user',
            [
               'type' => 'danger',
               'title' => 'Un email de confirmation n\'a pu vous être envoyé.',
               'message' => 'Connectez vous à votre compte et vérifié votre adresse mail.
               Tant que votre adresse email ne seras pas vérifié, vous ne pourrez pas poster de commentaire.',
            ]
        );
    }

    /**
     * User registration.
     *
     * @param User|null $user
     *
     * @return array
     */
    public function check(?User $user): array
    {
        // If user exist, we record it
        if (!empty($user)) {
            $user->setToken(null);
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $message = [
                'type' => 'info',
                'title' => 'Votre compte est maintenant validé.',
                'message' => '',
             ];
        }

        return $message = [
            'type' => 'info',
            'title' => 'Une erreur c\'est produite lors de la validation de votre compte.',
            'message' => '',
         ];
    }
}
