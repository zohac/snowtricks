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
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof Trick) {
            return;
        }

        $entity->setUser($this->tokenStorage->getToken()->getUser());
    }

    /**
     * Added the author of the trick modification.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof Trick) {
            return;
        }

        $entity->setModifiedBy($this->tokenStorage->getToken()->getUser());
        $entity->setDateModified(new \Datetime('NOW'));
    }
}
