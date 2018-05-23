<?php

namespace AppBundle\Service\Trick;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Form\Trick\AddType;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Add a new Trick.
 */
class Add
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
     * Add a trick in DB.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return FormView|null
     */
    public function add(Request $request, User $user): ?FormView
    {
        // 1) build the form
        $trick = new Trick();
        $form = $this->formFactory->create(AddType::class, $trick);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Add...
            $trick->setUser($user);

            // 5) save the Trick
            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            // Add a flash message
            $this->flashBag->add(
                'add_trick',
                [
                    'type' => 'success',
                    'title' => 'Nouveau trick bien enregistré!',
                    'message' => '',
                ]
            );

            return null;
        }
        // Return the form
        return $form->createView();
    }
}
