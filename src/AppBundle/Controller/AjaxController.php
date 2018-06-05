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
     * Home page.
     *
     * @Route("/ajax/trick/get", name="ST_get_trick_ajax")
     *
     * @param Request $request
     * @param ObjectManager $entityManager
     * @return Response
     */
    public function listAction(Request $request, ObjectManager $entityManager): Response
    {
        // Ajax request
        if ($request->isXMLHttpRequest()) {
            // Get the index value
            $index = $request->get('index');
            //Calculate the offset
            $offset = 4 * ($index + 2);
            $limit = 4;

            $view = 'AppBundle:Trick:ajax.html.twig';
        }

        // We recover all the tricks
        $listOfTricks = $entityManager
            ->getRepository(Trick::class)
            ->myFindAll($limit, $offset);

        // Return the view
        return $this->render($view, ['listOfTricks' => $listOfTricks]);
    }
}
