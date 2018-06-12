<?php

namespace tests\AppBundle\Controller;

use AppBundle\Controller\TrickController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    /**
     * @var [type]
     */
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Test the listAction.
     *
     * @see TrickController::listAction()
     */
    public function testList()
    {
        $this->client->request('GET', '/');

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }
}
