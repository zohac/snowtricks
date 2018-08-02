<?php

// src/AppBundle/Controller/CommentController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Utils\Comment\CommentTypeHandler;
use AppBundle\Form\Comment\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class allowing the management of Comments.
 */
class CommentController extends Controller
{
    /**
     * Delete a comment.
     *
     * @Route("/comment/delete/{id}/{token}", name="ST_comment_delete")
     * @ParamConverter("comment", options={"mapping"={"id"="id"}})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param ObjectManager $entityManager
     * @param Comment       $comment
     * @param string        $token
     *
     * @return Response
     */
    public function deleteAction(ObjectManager $entityManager, Comment $comment, string $token): Response
    {
        // If the token is valid
        if ($this->isCsrfTokenValid($comment->getId(), $token)) {
            // We're recording changes of the comment
            $entityManager->remove($comment);
            $entityManager->flush();
            // Adding the Flash Message
            $this->addFlash('success', 'Le commentaire est bien supprimé!');
            // Redirect to home
            return $this->redirectToRoute('ST_trick_show', ['slug' => $comment->getTrick()->getSlug()]);
        }

        throw new \LogicException(
            sprintf('Une erreur est survenu lors de la suppression du commentaire!')
        );
    }

    /**
     * Update a comment.
     *
     * @Route(
     *      "/comment/update/{id}",
     *      name="ST_comment_update",
     *      requirements={"id"="\d+"}
     * )
     * @ParamConverter("comment", options={"mapping"={"id"="id"}})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request         $request
     * @param FormTypeHandler $handler
     * @param Comment         $comment
     *
     * @return response
     */
    public function updateAction(
        Request $request,
        CommentTypeHandler $handler,
        Comment $comment
    ): response {
        // 1) Creating the form
        $form = $this->createForm(CommentType::class, $comment);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($handler->handle($form, $comment->getTrick())) {
            // Adding a Flash Message
            $this->addFlash('success', 'Le commentaire est bien mis à jour.');

            // Redirect to the trick detail
            return $this->redirectToRoute('ST_trick_show', ['slug' => $comment->getTrick()->getSlug()]);
        }
        // Else, return the form
        return $this->render('Comment/update.html.twig', ['form' => $form->createView()]);
    }
}
