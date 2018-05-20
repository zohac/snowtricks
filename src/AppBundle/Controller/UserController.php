<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\User\Update;
use AppBundle\Form\User\UpdateType;
use AppBundle\Service\User\Registration;
use AppBundle\Form\User\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class allowing the management of users.
 */
class UserController extends Controller
{
    /**
     * Register a new user.
     *
     * @Route("/registration", name="ST_registration")
     *
     * @param Request      $request
     * @param Registration $register
     *
     * @return Response
     */
    public function registrationAction(Request $request, Registration $register): Response
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Register a new user
            $register->registration($user);

            return $this->redirectToRoute('ST_registration');
        }

        return $this->render('User/registration.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Check the mail of a new user.
     *
     * @Route("/registration/{token}", name="ST_registration_check")
     * @Entity("user", expr="repository.getUserWithToken(token)")
     *
     * @Method({"GET"})
     *
     * @param User|null    $user
     * @param Registration $register
     *
     * @return Response
     */
    public function registrationCheckAction(?User $user, Registration $register): Response
    {
        // User registration
        $message = $register->check($user);

        // Add a flash message
        $this->addFlash('info', $message);

        // Redirect to home
        return $this->redirectToRoute('ST_registration');
    }

    /**
     * The login form.
     *
     * @Route("/login", name="ST_login")
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils): Response
    {
        return $this->render(
            'User/login.html.twig',
            [
                'last_username' => $authUtils->getLastUsername(),
                'error' => $authUtils->getLastAuthenticationError(),
            ]
        );
    }

    /**
     * @Route("/login_check", name="ST_login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout", name="ST_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * Update the user info.
     *
     * @Route("/user/update", name="ST_user_update")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Update             $updateUser
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function updateAction(Request $request, Update $updateUser, ?UserInterface $user): Response
    {
        // 1) We create the form
        $form = $this->createForm(UpdateType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Update a user
            $updateUser->update($user);
        }
        // The createView () method of the form is passed to the view
        // so that it can display the form all by itself.
        return $this->render('User/update.html.twig', ['form' => $form->createView()]);
    }
}
