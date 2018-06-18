<?php

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class SnowtrickTypeHandler
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
     *
     * @return bool
     */
    public function handle(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
