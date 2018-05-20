<?php

// src/AppBundle/Service/User/Update.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Update a user.
 */
class Update
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $entityManager,
        SessionInterface $session
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Update a user.
     *
     * @param User $user
     */
    public function update(User $user)
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        }

        // 3) save the User!
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 4) Add a flash message
        $this->flashBag->add('info', 'Vos infos sont bien misent à jour.');
    }
}
