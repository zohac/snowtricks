<?php

namespace AppBundle\Service\Comment;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddComment
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     * @param UserInterface $user
     */
    public function __construct(ObjectManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * Add new comment
     *
     * @param Trick $trick
     * @param string $content
     * @return void
     */
    public function add(Trick $trick, string $content)
    {
        // Add a new comment
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setTrick($trick);
        $comment->setUser($this->user);
        $comment->setDate(new \Datetime('NOW'));

        // Save the comment
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}
