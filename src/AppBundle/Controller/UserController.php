<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\User\Registration;
use AppBundle\Form\User\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
}
