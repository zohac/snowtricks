<?php

namespace AppBundle\Utils\Trick;

use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrickTypeHandler
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
     * @param ObjectManager         $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param ThumbnailGenerator    $thumbnailGenerator
     */
    public function __construct(
        ObjectManager $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
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
    public function handle(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            //var_dump($form->getData()); die;
            $trick = $form->getData();

            // Set the authenticated user
            $trick->setUser($this->tokenStorage->getToken()->getUser());

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
