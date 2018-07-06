<?php

namespace AppBundle\Utils\Trick;

use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateTrickTypeHandler
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
    public function handle(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();

            // Set the authenticated user
            $trick->setModifiedBy($this->tokenStorage->getToken()->getUser());
            $trick->setDateModified(new \Datetime('NOW'));

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
