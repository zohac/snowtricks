<?php

// Non-functional test

namespace tests\AppBundle\Service\Trick;

use AppBundle\Entity\User;
use AppBundle\Service\Trick\Add;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

class AddTest extends TestCase
{
    private $objectManager;
    private $sessionInterface;
    private $formFactory;
    private $flashBag;

    public function setUp()
    {
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->sessionInterface = $this->createMock(Session::class);
        $this->flashBag = $this->createMock(FlashBag::class);
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->willReturn(null);

        $this->sessionInterface
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($this->flashBag);
    }

    public function testGetFormView()
    {
        $request = new Request();
        //Request::createFromGlobals();
        $user = new User();

        $addTrick = new Add($this->objectManager, $this->sessionInterface, $this->formFactory);
        $form = $addTrick->add($request, $user);

        $this->assertInstanceOf(FormView::class, $form);
    }

    public function tearDown()
    {
        $this->objectManager = null;
        $this->sessionInterface = null;
        $this->flashBag = null;
        $this->formFactory = null;
    }
}
