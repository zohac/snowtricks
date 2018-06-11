<?php

// src/AppBundle/Service/User/Registration.php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormView;
use AppBundle\Service\Email\UserMailer;
use AppBundle\Form\User\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Register a new user.
 */
class Registration
{
    /**
     * @var UserMailer
     */
    private $mailer;

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
     * @param UserMailer                   $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ObjectManager                $entityManager
     * @param SessionInterface             $session
     * @param FormFactoryInterface         $formFactory
     */
    public function __construct(
        UserMailer $mailer,
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $entityManager,
        SessionInterface $session,
        FormFactoryInterface $formFactory
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->formFactory = $formFactory;
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     *
     * @return FormView|null
     */
    public function registration(Request $request): ?FormView
    {
        // 1) build the form
        $form = $this->formFactory->create(RegistrationType::class);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);

            // 1) Encode the password
            $password = $this->passwordEncoder->encodePassword($user, $data['plainPassword']);
            $user->setPassword($password);
            // 2) Set a token for registration
            $user->setToken(hash('sha256', serialize($user).microtime()));
            $user->setRoles([]);

            // 3) save the User!
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // 4) Send a confirmation mail
            if ($this->mailer->sendNewRegistration($user)) {
                $this->flashBag->add(
                    'update_user',
                    [
                        'type' => 'info',
                        'title' => 'Vérifiez votre email, pour confirmer votre inscription.',
                        'message' => '',
                    ]
                );

                return null;
            }
            // 5) In case of error
            $this->flashBag->add(
                'update_user',
                [
                    'type' => 'danger',
                    'title' => 'Un email de confirmation n\'a pu vous être envoyé.',
                    'message' => 'Connectez vous à votre compte et vérifié votre adresse mail.
                    Tant que votre adresse email ne seras pas vérifié, vous ne pourrez pas poster de commentaire.',
                ]
            );

            return null;
        }

        return $form->createView();
    }

    /**
     * User registration.
     *
     * @param User|null $user
     */
    public function validate(?User $user)
    {
        // If the user doesn't exist
        if (!$user) {
            throw new \LogicException(
                sprintf('L\'utilisateur n\'existe pas! Avez vous bien suivi le lien envoyé par email!')
            );
        }
        // If user exist, we record it
        $user->setToken(null);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->flashBag->add(
            'update_user',
            [
                'type' => 'info',
                'title' => 'Votre compte est maintenant validé.',
                'message' => '',
            ]
        );
    }
}
