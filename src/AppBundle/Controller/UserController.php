<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\User\UserType;
use AppBundle\Service\User\Update;
use AppBundle\Utils\User\Registration;
use AppBundle\Form\User\RegistrationType;
use AppBundle\Service\User\ResetPassword;
use AppBundle\Utils\User\UserTypeHandler;
use AppBundle\Form\User\ResetPasswordType;
use AppBundle\Service\User\ForgotPassword;
use AppBundle\Form\User\ForgotPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\User\RegistrationTypeHandler;
use AppBundle\Utils\User\ForgotPasswordTypeHandler;
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
     * @Route("/registration", name="ST_registration",
     *      options={"menu": {
     *          "id": "main",
     *          "name": "Inscription",
     *          "order": 3
     *      }})
     *
     * @param Request                 $request
     * @param RegistrationTypeHandler $handler
     *
     * @return Response
     */
    public function registrationAction(Request $request, RegistrationTypeHandler $handler): Response
    {
        // Build the form
        $form = $this->createForm(RegistrationType::class);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('info', 'Vérifiez votre boîte email, pour confirmer votre inscription.');
            // Redirect to home
            return $this->redirectToRoute('ST_index');
        }
        // Render the form
        return $this->render('User/registration.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Check the mail of a new user.
     *
     * @Route("/registration/{token}",
     *      name="ST_registration_check",
     *      requirements={"token"="[0-9a-f]{64}$"}
     * )
     * @Entity("user", expr="repository.getUserWithToken(token)")
     *
     * @Method({"GET"})
     *
     * @param User         $user
     * @param Registration $register
     *
     * @return Response
     */
    public function validateUserAction(User $user, Registration $register): Response
    {
        // User registration
        $register->validate($user);

        // Redirect to home
        return $this->redirectToRoute('ST_login');
    }

    /**
     * The login form.
     *
     * @Route("/login", name="ST_login",
     *      options={"menu": {
     *          "id": "main",
     *          "name": "Connexion",
     *          "order": 2
     *      }})
     *
     * @param AuthenticationUtils $authUtils
     *
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils): Response
    {
        // Render the login form
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
     * @Route("/logout", name="ST_logout",
     *      options={"menu": {
     *          "id": "user",
     *          "name": "Déconnexion",
     *      }})
     */
    public function logoutAction()
    {
    }

    /**
     * Update the user info.
     *
     * @Route("/user/update", name="ST_user_update",
     *      options={"menu": {
     *          "id": "user",
     *          "name": "Mon compte",
     *          "order": 2
     *      }})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request            $request
     * @param Update             $updateUser
     * @param UserInterface|null $user
     *
     * @return Response
     */
    public function updateAction(Request $request, UserTypeHandler $handler, ?UserInterface $user): Response
    {
        // Build the form
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('success', 'Vos infos sont bien misent à jour.');
            // Redirect to home
            return $this->redirectToRoute('ST_index');
        }
        // Render the form
        return $this->render('User/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Provide an email link to change a password.
     *
     * @Route("/password/forgot", name="ST_forgot_password")
     *
     * @param Request        $request
     * @param ForgotPassword $forgotPassword
     *
     * @return Response
     */
    public function forgotPasswordAction(Request $request, ForgotPasswordTypeHandler $handler): Response
    {
        // Build the form
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('info', 'Nous vous avons envoyé un e-mail pour réinitialiser votre mot de passe.');
            // Redirect to home
            return $this->redirectToRoute('ST_index');
        }
        // Render the form
        return $this->render('User/forgot_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Reset a password.
     *
     * @Route("/password/reset/{token}",
     *      name="ST_reset_password",
     *      requirements={"token"="[0-9a-f]{64}$"}
     * )
     * @Entity("user", expr="repository.getUserWithToken(token)")
     *
     * @param ResetPassword $resetPassword
     * @param User|null     $user
     * @param Request       $request
     *
     * @return Response
     */
    public function resetPasswordAction(Request $request, UserTypeHandler $handler, ?User $user): Response
    {
        // Build the form
        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('success', 'Votre mot de passe est bien mis à jour.');
            // Redirect to the login page
            return $this->redirectToRoute('ST_login');
        }
        // Render the form
        return $this->render('User/reset_password.html.twig', ['form' => $form->createView()]);
    }
}
