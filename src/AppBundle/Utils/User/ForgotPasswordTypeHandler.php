<?php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use AppBundle\Events\ResetPasswordEvent;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ForgotPasswordTypeHandler
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param ObjectManager   $entityManager
     * @param SessionInterface         $session
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectManager $entityManager,
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
