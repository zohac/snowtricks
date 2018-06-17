<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Comment;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentListener
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
        // If the user is not connected, the listener is exited.
        // The case of fixtures
        if (!$this->tokenStorage->getToken()) {
            return;
        }

        // We're getting the comment.
        $entity = $args->getEntity();
        // only act on some "comment" entity
        if (!$entity instanceof Comment) {
            return;
        }

        // Set the authenticated user
        $entity->setUser($this->tokenStorage->getToken()->getUser());
    }
}
