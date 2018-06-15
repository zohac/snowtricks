<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use AppBundle\Service\Trick\Add;
use AppBundle\Form\Trick\AddTrickType;
use AppBundle\Service\Comment\AddComment;
use AppBundle\Utils\Trick\TrickTypeHandler;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TrickController extends Controller
{
    /**
     * Add a new trick.
     *
     * @Route("/trick/add", name="ST_trick_add",
     *      options={"menu": {
     *          "id": "user",
     *          "name": "Ajouter un Trick",
     *          "order": 1
     *      }})
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request          $request
     * @param TrickTypeHandler $handler
     *
     * @return Response
     */
    public function addAction(Request $request, TrickTypeHandler $handler): Response
    {
        // Build the form
        $form = $this->createForm(AddTrickType::class);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('success', 'Nouveau trick bien enregistré!');

            return $this->redirectToRoute('ST_index');
        }
        // Redirect to home
        return $this->render('Trick/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Update a trick.
     *
     * @Route("/trick/update/{slug}", name="ST_trick_update")
     * @Entity("trick", expr="repository.FindWithAllEntities(slug)")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request          $request
     * @param Trick            $trick
     * @param TrickTypeHandler $handler
     *
     * @return Response
     */
    public function updateAction(Request $request, Trick $trick, TrickTypeHandler $handler): Response
    {
        // Build the form
        $form = $this->createForm(AddTrickType::class, $trick);

        $form->handleRequest($request);
        if ($handler->handle($form)) {
            // Add a flash message
            $this->addFlash('success', 'Le trick est bien mis à jour!');
            // Redirect to home
            return $this->redirectToRoute('ST_index');
        }
        // If the trick wasn't added successfully, we render the form
        return $this->render('Trick/update.html.twig', ['form' => $form->createView(), 'trick' => $trick]);
    }

    /**
     * Home page.
     *
     * @Route("/", name="ST_index")
     *
     * @param ObjectManager $entityManager
     *
     * @return Response
     */
    public function listAction(ObjectManager $entityManager): Response
    {
        // We recover all the tricks
        $listOfTricks = $entityManager->getRepository(Trick::class)->findAllWithAllEntities();

        // Return the view
        return $this->render('Trick/index.html.twig', ['listOfTricks' => $listOfTricks]);
    }

    /**
     * Show a single trick.
     *
     * @Route("/trick/show/{slug}", name="ST_trick_show")
     * @Entity("trick", expr="repository.FindWithAllEntities(slug)")
     *
     * @param Request       $request
     * @param Trick         $trick
     * @param AddComment    $addComment
     * @param ObjectManager $entityManager
     *
     * @return Response
     */
    public function showAction(
        Request $request,
        Trick $trick,
        AddComment $addComment,
        ObjectManager $entityManager
    ): Response {
        if ($formView = $addComment->add($request, $trick)) {
            // Get the form and the list of tricks
            $listOfComment = $entityManager->getRepository(Comment::class)->findWithAllEntities($trick);

            // Return the view
            return $this->render(
                'Trick/show.html.twig',
                [
                    'trick' => $trick,
                    'listOfComment' => $listOfComment,
                    'form' => $formView,
                ]
            );
        }
        // Adding a Flash Message
        $this->addFlash(
            'add_comment',
            [
                'type' => 'success',
                'title' => 'Nouveau commentaire bien enregistré!',
                'message' => '',
            ]
        );
        // Redirect to trick
        return $this->redirectToRoute('ST_trick_show', ['slug' => $trick->getSlug()]);
    }

    /**
     * Delete a trick.
     *
     * @Route("/trick/delete/{slug}/{token}", name="ST_trick_delete")
     * @ParamConverter("trick", options={"mapping"={"slug"="slug"}})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Trick  $trick
     * @param string $token
     *
     * @return Response
     */
    public function deleteAction(Trick $trick, string $token): Response
    {
        // If the token is valid
        if ($this->isCsrfTokenValid($trick->getSlug(), $token)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

            // Add a flash message
            $this->flashBag->add('success', 'Le trick est bien supprimé!');
            // Redirect to home
            return $this->redirectToRoute('ST_index');
        }

        throw new \LogicException(
            sprintf('Une erreur est survenu lors de la suppression du Trick!')
        );
    }
}
