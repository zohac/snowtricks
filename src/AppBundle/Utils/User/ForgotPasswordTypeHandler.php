<?php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ForgotPasswordTypeHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param SessionInterface         $session
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->eventDispatcher = $eventDispatcher;
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
            $data = $form->getData();
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($data['emailRecovery']);

            //If the user exist
            if ($user) {
                // dispatcher
                $this->eventDispatcher->dispatch(ResetPasswordEvent::NAME, new ResetPasswordEvent($user));

                // 3) Set a token for ressetting password
                $user->setToken(hash('sha256', serialize($user).microtime()));

                // 5) save the User!
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return true;
            }
            $this->flashBag->add('danger', 'Aucun utilisateur avec cette adresse email.');
        }

        return false;
    }
}
