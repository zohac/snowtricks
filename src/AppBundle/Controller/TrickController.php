<?php

namespace AppBundle\Controller;

use AppBundle\Service\Trick\Add;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TrickController extends Controller
{
    /**
     * Add a new trick.
     *
     * @Route("/trick/add", name="ST_trick_add")
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, ?UserInterface $user, Add $addTrick)
    {
        // Creating the form to add a Trick
        if ($form = $addTrick->add($request, $user)) {
            // If the trick wasn't added successfully, we render the form
            return $this->render('Trick/add.html.twig', ['form' => $form]);
        }
        // Redirect to home
        return $this->redirectToRoute('ST_index');
    }

    /**
     * Home page.
     *
     * @Route("/", name="ST_index")
     */
    public function listAction()
    {
        return $this->render('layout.html.twig');
    }
}
