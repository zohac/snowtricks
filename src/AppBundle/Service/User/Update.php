<?php

// src/AppBundle/Service/User/Update.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Form\User\UpdateType;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Update a user.
 */
class Update
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ObjectManager                $entityManager
     * @param SessionInterface             $session
     * @param FormFactoryInterface         $formFactory
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $entityManager,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->formFactory = $formFactory;
    }

    /**
     * Update a user.
     *
     * @param User $user
     */
    public function update(Request $request, User $user): ?FormView
    {
        // 1) We create the form
        $form = $this->formFactory->create(UpdateType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            }
            // 3) save the User!
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // 4) Add a flash message
            $this->flashBag->add(
                'update_user',
                [
                    'type' => 'info',
                    'title' => 'Vos infos sont bien misent à jour.',
                    'message' => '',
                ]
            );

            return null;
        }

        return $form->createView();
    }
}
