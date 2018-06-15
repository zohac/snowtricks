<?php

namespace AppBundle\Service\Comment;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use Symfony\Component\Form\FormView;
use AppBundle\Form\Comment\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     * @param UserInterface $user
     */
    public function __construct(
        ObjectManager $entityManager,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->formFactory = $formFactory;
    }

    /**
     * Add new comment.
     *
     * @param Trick  $trick
     * @param string $content
     */
    public function add(Request $request, Trick $trick): ?FormView
    {
        // Build the form
        $form = $this->formFactory->create(CommentType::class);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Add a new comment
            $comment = new Comment();
            $comment->setContent($data['content']);
            $comment->setTrick($trick);
            $comment->setUser($this->user);
            $comment->setDate(new \Datetime('NOW'));

            // Save the comment
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            // Redirect to trick
            return null;
        }

        return $form->createView();
    }
}
