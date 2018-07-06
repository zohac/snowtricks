<?php

namespace AppBundle\Utils\Comment;

use AppBundle\Entity\Trick;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentTypeHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handle(FormInterface $form, Trick $trick): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            // Set the authenticated user
            $comment->setTrick($trick);
            $comment->setUser($this->tokenStorage->getToken()->getUser());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
