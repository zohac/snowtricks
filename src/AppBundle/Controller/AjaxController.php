<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trick;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AjaxController extends Controller
{
    /**
     * Check a title of a trick.
     *
     * @Route("/ajax/checkTitle", name="ST_check_title_ajax")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function checkTitleAjaxAction(Request $request): Response
    {
        $title = filter_var($request->get('title'), FILTER_SANITIZE_STRING);

        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneByTitle($title);

        $response = (empty($trick)) ? 'true' : 'false';

        return new Response($response);
    }

    /**
     * Home page.
     *
     * @Route("/ajax/trick/get", name="ST_get_trick_ajax")
     *
     * @param Request       $request
     * @param ObjectManager $entityManager
     *
     * @return Response
     */
    public function listAction(Request $request, ObjectManager $entityManager): Response
    {
        // Ajax request
        if ($request->isXMLHttpRequest()) {
            // Get the index value
            $index = (int) $request->get('index');
            //Calculate the offset
            $offset = 4 * ($index + 2);
            $limit = 4;
        }

        // We recover all the tricks
        $listOfTricks = $entityManager->getRepository(Trick::class)->findAllWithAllEntities($limit, $offset);

        // Return the view
        return $this->render('Trick\ajax.html.twig', ['listOfTricks' => $listOfTricks]);
    }
}