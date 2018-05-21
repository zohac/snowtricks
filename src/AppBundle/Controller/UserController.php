<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\User\Update;
use AppBundle\Form\User\UpdateType;
use AppBundle\Service\User\UserQuery;
use AppBundle\Service\User\Registration;
use AppBundle\Form\User\RegistrationType;
use AppBundle\Service\User\ForgotPassword;
use AppBundle\Form\User\ForgotPasswordType;
use AppBundle\EventSubscriber\UserSubscriber;
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
class UserController extends Controller implements UserSubscripterController
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
        $this->addFlash('registration_check', $message);

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
     * @param Request            $request
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

    /**
     * Provide an email link to change a password.
     *
     * @Route("/password/forgot", name="ST_forgot_password")
     *
     * @param Request        $request
     * @param ForgotPassword $forgotPassword
     * @param UserSubscriber $UserSubscriber
     *
     * @return Response
     */
    public function forgotPasswordAction(
        Request $request,
        ForgotPassword $forgotPassword,
        UserSubscriber $UserSubscriber
    ): Response {
        $user = $UserSubscriber->getUser();
        // Build the form
        $form = $this->createForm(ForgotPasswordType::class, $user);

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Register a new user
            $forgotPassword->forgotPassword($user);
            // Redirect to home
            return $this->redirectToRoute('ST_registration');
        }

        // Return the view
        return $this->render('User/forgot_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Reset a password.
     *
     * @Route("/password/reset/{token}", name="ST_reset_password")
     * @Entity("user", expr="repository.getUserWithToken(token)")
     *
     * @param UserQuery $userQuery
     * @param User|null $user
     * @param Request   $request
     *
     * @return Response
     */
    public function resetPasswordAction(UserQuery $userQuery, ?User $user, Request $request): Response
    {
        $form = $userQuery->resetPassword($request, $user);
        if (!$form) {
            // Redirect to home
            return $this->redirectToRoute('ST_registration');
        }

        // The form is passed to the view, so that it can display the form all by itself
        return $this->render('User/reset_password.html.twig', ['form' => $form]);
    }
}
