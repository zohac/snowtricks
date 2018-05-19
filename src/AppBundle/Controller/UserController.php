<?php

// src/AppBundle/Controller/UserController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\User\Registration;
use AppBundle\Form\User\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @param Registration $register
     */
    public function registrationAction(Request $request, Registration $register)
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
     */
    public function registrationCheckAction()
    {
    }
}
