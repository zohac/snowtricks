<?php

namespace AppBundle\Service\User;

use AppBundle\Entity\User;
use AppBundle\Form\User\ResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class managing password replacement.
 */
class UserQuery
{
    /**
     * @var RegistrationMailer
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
     * @var RouterInterface
     */
    private $router;

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
        FormFactoryInterface $formFactory,
        RouterInterface  $router
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function register()
    {
    }

    public function update()
    {
    }

    public function forgotPassword()
    {
    }

    /**
     * Undocumented function.
     *
     * @param Request   $request
     * @param User|null $user
     */
    public function resetPassword(Request $request, ?User $user)
    {
        // If the user doesn't exist
        if (!$user) {
            throw new \LogicException(
                sprintf('L\'utilisateur n\'existe pas! Avez vous bien suivi le lien envoyé par email!')
            );
        }

        // 1) Creat the form
        $form = $this->formFactory->create(ResetPasswordType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Reset the password
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set the token to null
            $user->setToken(null);

            // save the User!
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add(
                'reset_password',
                [
                    'type' => 'success',
                    'title' => 'Votre mot de passe est mis à jour.',
                    'message' => '',
                ]
            );

            return;
        }
        // Return the view
        return $form->createView();
    }
}
