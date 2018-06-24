<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Trick;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrickListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Added the author of the trick.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        // We're getting the Trick.
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof Trick) {
            return;
        }

        // Set the authenticated user
        $entity->setUser($this->tokenStorage->getToken()->getUser());
    }

    /**
     * Added the author of the trick modification.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        // We're getting the Trick.
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof Trick) {
            return;
        }

        // Set the authenticated user and the date of the modification.
        $entity->setModifiedBy($this->tokenStorage->getToken()->getUser());
        $entity->setDateModified(new \Datetime('NOW'));
    }
}
