<?php

namespace AppBundle\Service\Trick;

use AppBundle\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeleteTrick
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param ObjectManager        $entityManager
     * @param SessionInterface     $session
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        ObjectManager $entityManager,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->formFactory = $formFactory;
    }

    /**
     * Delete a trick.
     *
     * @param Request $request
     * @param Trick   $trick
     */
    public function delete(Trick $trick)
    {
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        // Add a flash message
        $this->flashBag->add(
            'delete_trick',
            [
                'type' => 'success',
                'title' => 'Le trick est bien supprimé!',
                'message' => '',
            ]
        );
    }
}
