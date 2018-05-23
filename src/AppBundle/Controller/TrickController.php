<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trick;
use AppBundle\Service\Trick\Add;
use AppBundle\Service\Trick\UpdateTrick;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TrickController extends Controller
{
    /**
     * Add a new trick.
     *
     * @Route("/trick/add", name="ST_trick_add")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request            $request
     * @param UserInterface|null $user
     * @param Add                $addTrick
     *
     * @return Response
     */
    public function addAction(Request $request, ?UserInterface $user, Add $addTrick): Response
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
     * Update a trick.
     *
     * @Route("/trick/update/{slug}", name="ST_trick_update")
     * @Entity("trick", expr="repository.FindWithAllEntities(slug)")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request            $request
     * @param UserInterface|null $user
     * @param UpdateTrick        $updateTrick
     * @param Trick              $trick
     *
     * @return Response
     */
    public function updateAction(
        Request $request,
        ?UserInterface $user,
        UpdateTrick $updateTrick,
        Trick $trick
    ): Response {
        // Creating the form to add a Trick
        if ($form = $updateTrick->update($request, $user, $trick)) {
            // If the trick wasn't added successfully, we render the form
            return $this->render(
                'Trick/update.html.twig',
                [
                    'form' => $form,
                    'trick' => $trick,
                ]
            );
        }
        // Redirect to home
        return $this->redirectToRoute('ST_index');
    }

    /**
     * Home page.
     *
     * @Route("/", name="ST_index")
     *
     * @param ObjectManager $entityManager
     * @return Response
     */
    public function listAction(ObjectManager $entityManager): Response
    {
        // We recover all the tricks
        $listOfTricks = $entityManager->getRepository(Trick::class)->findAll();

        // Return the view
        return $this->render('Trick/index.html.twig', ['listOfTricks' => $listOfTricks]);
    }

    /**
     * Home page.
     *
     * @Route("/trick/show/{slug}", name="ST_trick_show")
     * @Entity("trick", expr="repository.FindWithAllEntities(slug)")
     */
    public function showAction(Trick $trick): Response
    {
        // Return the view
        return $this->render('Trick/show.html.twig', ['trick' => $trick]);
    }
}
