<?php

namespace AppBundle\Utils;

use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;

class FormTypeHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
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
