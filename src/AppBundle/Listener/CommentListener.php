<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use AppBundle\Events\AddCommentEvent;
use AppBundle\Listener\CommentListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Trick
     */
    private $trick;

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
        // We're getting the comment.
        $entity = $args->getEntity();
        // only act on some "comment" entity
        if (!$entity instanceof Comment) {
            return;
        }

        // Set the authenticated user
        $entity->setTrick($this->trick);
        $entity->setUser($this->tokenStorage->getToken()->getUser());
    }

    /**
     * Undocumented function.
     *
     * @param AddCommentEvent $event
     */
    public function onAddCommentEvent(AddCommentEvent $event)
    {
        $this->trick = $event->getTrick();
    }
}
