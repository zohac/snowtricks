<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Service\Email\UserMailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserMailer
     */
    private $mailer;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserPasswordEncoderInterface $passwordEncoder,
        UserMailer $mailer,
        SessionInterface $session
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        // We're getting the Trick.
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof User) {
            return;
        }

        // 1) Encode the password
        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
        $entity->setPassword($password);
        // 2) Set a token for registration
        $entity->setToken(hash('sha256', serialize($entity).microtime()));
        $entity->setRoles([]);

        // 3) Send a confirmation mail
        if ($this->mailer->sendNewRegistration($entity)) {
            $this->flashBag->add('info', 'Vérifiez votre email, pour confirmer votre inscription.');

            return;
        }
        // 4) In case of error
        $this->flashBag->add(
            'danger',
            'Un email de confirmation n\'a pu vous être envoyé.
            Connectez vous à votre compte et vérifié votre adresse mail. 
            Tant que votre adresse email ne seras pas vérifié,
            vous ne pourrez pas poster de Trick ou des commentaires.'
        );
    }

    /**
     * Send a mail after register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // 1) We're getting the Trick.
        $entity = $args->getEntity();

        // 2) only act on some "Trick" entity
        if (!$entity instanceof User) {
            return;
        }

        // 3) Send a confirmation mail
        if ($this->mailer->sendNewRegistration($entity)) {
            $this->flashBag->add('info', 'Vérifiez votre email, pour confirmer votre inscription.');

            return;
        }
        // 4) In case of error
        $this->flashBag->add(
            'danger',
            'Un email de confirmation n\'a pu vous être envoyé.
            Connectez vous à votre compte et vérifié votre adresse mail. 
            Tant que votre adresse email ne seras pas vérifié,
            vous ne pourrez pas poster de Trick ou des commentaires.'
        );
    }

    /**
     * Added the author of the trick modification.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        // We're getting the Trick.
        $entity = $args->getEntity();

        // only act on some "Trick" entity
        if (!$entity instanceof User) {
            return;
        }

        // Set the authenticated user and the date of the modification.
        $entity->setModifiedBy($this->tokenStorage->getToken()->getUser());
        $entity->setDateModified(new \Datetime('NOW'));
    }
}
