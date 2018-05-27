<?php

// src/AppBundle/Controller/CommentController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
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
                'type' => 'danger',
                'title' => 'Le commentaire est bien supprimé!',
                'message' => '',
            ];
        }
        // Adding the Flash Message
        $this->addFlash($type, $message);
        // Redirect to home
        return $this->redirectToRoute('ST_trick_show', ['slug' => $comment->getTrick()->getSlug()]);
    }
}
