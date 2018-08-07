<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Trick;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AjaxController extends Controller
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Create new normalizer
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        // Create new serializer
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
    }

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
        // filter the title
        $title = filter_var($request->get('title'), FILTER_SANITIZE_STRING);

        // Check if a trick exist with this title
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findOneByTitle($title);

        // Set response
        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($trick, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Home page.
     *
     * @Route("/ajax/trick/get", name="ST_get_trick_ajax")
     * @Method({"POST"})
     *
     * @param Request       $request
     * @param ObjectManager $entityManager
     *
     * @return Response
     */
    public function listAction(Request $request, ObjectManager $entityManager): JsonResponse
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

        // Set token for delete link
        foreach ($listOfTricks as $key => $trick) {
            $csrf = $this->get('security.csrf.token_manager');
            $trick->getUser()->setToken($csrf->refreshToken($trick->getSlug())->getValue());
            $listOfTricks[$key] = $trick;
        }

        // Set response
        $response = new JsonResponse();
        $response->setContent($this->serializer->serialize($listOfTricks, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
