<?php

// src/AppBundle/Controller/CommentController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
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
        // Flash message initialization
        $type = 'remove_comment';
        $message = [
            'type' => 'danger',
            'title' => 'Une erreur c\'est produite!',
            'message' => 'Le commentaire n\'a pu être supprimé!',
        ];
        // If the token is valid
        if ($this->isCsrfTokenValid($comment->getId(), $token)) {
            // We're recording changes of the comment
            $entityManager->remove($comment);
            $entityManager->flush();
            // Changing the Flash Message
            $message = [
                'type' => 'success',
                'title' => 'Le commentaire est bien supprimé!',
                'message' => '',
            ];
        }
        // Adding the Flash Message
        $this->addFlash($type, $message);
        // Redirect to home
        return $this->redirectToRoute('ST_trick_show', ['slug' => $comment->getTrick()->getSlug()]);
    }

    /**
     * Update a comment.
     *
     * @Route("/comment/update/{id}", name="ST_comment_update")
     * @ParamConverter("comment", options={"mapping"={"id"="id"}})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param ObjectManager $entityManager
     * @param Request       $request
     * @param Comment       $comment
     *
     * @return response
     */
    public function updateAction(ObjectManager $entityManager, Request $request, Comment $comment): response
    {
        // 1) Creating the form
        $form = $this->createForm(CommentType::class, $comment);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the comment
            $entityManager->persist($comment);
            $entityManager->flush();

            // Adding a Flash Message
            $this->addFlash(
                'update_comment',
                [
                    'type' => 'success',
                    'title' => 'Le commentaire est bien mis à jour.',
                    'message' => '',
                ]
            );

            // Redirect to the trick detail
            return $this->redirectToRoute('ST_trick_show', ['slug' => $comment->getTrick()->getSlug()]);
        }

        return $this->render('Comment/update.html.twig', ['form' => $form->createView()]);
    }
}
