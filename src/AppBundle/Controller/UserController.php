<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\User\Update;
use AppBundle\Service\User\UserQuery;
use AppBundle\Service\User\Registration;
use AppBundle\Service\User\ResetPassword;
use AppBundle\Service\User\ForgotPassword;
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
     * @param Request      $request
     * @param Registration $register
     *
     * @return Response
     */
    public function registrationAction(Request $request, Registration $register): Response
    {
        if ($form = $register->registration($request)) {
            return $this->render('User/registration.html.twig', ['form' => $form]);
        }
        // Redirect to home
        return $this->redirectToRoute('ST_index');
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
    public function registrationValidateAction(?User $user, Registration $register): Response
    {
        // User registration
        $register->validate($user);

        // Redirect to home
        return $this->redirectToRoute('ST_registration');
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
    public function updateAction(Request $request, Update $updateUser, ?UserInterface $user): Response
    {
        if ($form = $updateUser->update($request, $user)) {
            return $this->render('User/update.html.twig', ['form' => $form]);
        }
        // Redirect to home
        return $this->redirectToRoute('ST_index');
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
    public function forgotPasswordAction(Request $request, ForgotPassword $forgotPassword): Response
    {
        if ($form = $forgotPassword->getForm($request)) {
            return $this->render('User/forgot_password.html.twig', ['form' => $form]);
        }
        // Redirect to home
        return $this->redirectToRoute('ST_index');
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
    public function resetPasswordAction(ResetPassword $resetPassword, ?User $user, Request $request): Response
    {
        if ($form = $resetPassword->reset($request, $user)) {
            // The form is passed to the view, so that it can display the form all by itself
            return $this->render('User/reset_password.html.twig', ['form' => $form]);
        }
        // Redirect to home
        return $this->redirectToRoute('ST_login');
    }
}
