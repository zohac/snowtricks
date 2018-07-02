<?php

// src/AppBundle/Service/User/Registration.php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Register a new user.
 */
class Registration
{
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
     * @param ObjectManager    $entityManager
     * @param SessionInterface $session
     */
    public function __construct(ObjectManager $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * User registration.
     *
     * @param User $user
     */
    public function validate(User $user)
    {
        // If user exist, we record it
        $user->setToken(null);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->flashBag->add('info', 'Votre compte est maintenant validé.');
    }
}
