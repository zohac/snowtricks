<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use AppBundle\Service\Email\UserMailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Events\UserEmailChangedEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcher
     */
    private $eventDispatcher;

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
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        // We're getting the User.
        $entity = $args->getEntity();

        // only act on some "User" entity
        if (!$entity instanceof User) {
            return;
        }

        // 1) Encode the password
        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
        $entity->setPassword($password);
        // 2) Set a token for registration
        $entity->setToken(hash('sha256', serialize($entity).microtime()));
        // 3) Set Role
        $entity->setRoles([]);
        // 4) Set date of the registration.
        $entity->setDateRegistration(new \Datetime('NOW'));

        $avatar = new Picture();
        $avatar->setName('user.svg');
        $avatar->setPath(Picture::DEFAULT_USER);
        $entity->setAvatar($avatar);
    }

    /**
     * Send a mail after register a new User.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // 1) We're getting the User.
        $entity = $args->getEntity();

        // 2) only act on some "User" entity
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
     * Update a User.
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs  $args)
    {
        // 1) We're getting the User.
        $entity = $args->getEntity();

        // 2) only act on some "User" entity
        if (!$entity instanceof User) {
            return;
        }

        // 3) Encode the password
        if ($entity->getPlainPassword()) {
            $password = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);
        }

        // 4) If the email has changed
        if ($args->hasChangedField('email')) {
            // 5) Set a token for registration and change the role
            $entity->setRoles([]);
            $entity->setToken(hash('sha256', serialize($entity).microtime()));
            // 6) Send an email
            if ($this->mailer->sendNewRegistration($entity)) {
                $this->flashBag->add(
                    'info',
                    'Vérifiez votre boîte mail, pour confirmer votre nouvel email.'
                );
                // 7) logout the user
                $event = new UserEmailChangedEvent();
                $this->eventDispatcher->dispatch(UserEmailChangedEvent::NAME, $event);
                return;
            }
            // 8) In case of error
            $this->flashBag->add(
                'danger',
                'Un email de confirmation n\'a pu vous être envoyé.
                Connectez vous à votre compte et vérifié votre adresse mail. 
                Tant que votre adresse email ne seras pas vérifié,
                vous ne pourrez pas poster de Trick ou des commentaires.'
            );
        }
    }
}
