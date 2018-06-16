<?php

namespace AppBundle\Utils\Comment;

use AppBundle\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class CommentTypeHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     * @param Trick         $trick
     *
     * @return bool
     */
    public function handle(FormInterface $form, Trick $trick): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if ($trick) {
                $form->getData()->setTrick($trick);
            }
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handleForUpdate(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
