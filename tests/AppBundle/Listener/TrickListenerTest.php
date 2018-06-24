<?php

namespace tests\AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Listener\TrickListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrickListenerTest extends TestCase
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var InteractiveLoginEvent
     */
    private $tokenStorage;
    /**
     * @var LifecycleEventArgs
     */
    private $args;

    /**
     * @var Trick
     */
    private $trick;

    protected function setUp()
    {
        parent::setUp();

        $this->trick = new Trick();

        $this->token = $this
            ->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMockForAbstractClass();

        $this->tokenStorage = $this
            ->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMockForAbstractClass();
        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->willReturn($this->token);
        $this->args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity'])
            ->getMock();
    }

    public function testPrePersist()
    {
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->trick);

        $event = new TrickListener($this->tokenStorage);

        $event->prePersist($this->args);

        $this->assertContainsOnlyInstancesOf(User::class, [$this->trick->getUser()]);
    }

    public function testPrePersistWithInvalidToken()
    {
        $this->tokenStorage = $this
            ->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMockForAbstractClass();
        $this->tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $event = new TrickListener($this->tokenStorage);

        $event->prePersist($this->args);
    }

    public function testPrePersistWithInvalidTrick()
    {
        $this->args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity'])
            ->getMock();
        $this->args
        ->expects($this->once())
        ->method('getEntity')
        ->willReturn(new User());

        $event = new TrickListener($this->tokenStorage);

        $event->prePersist($this->args);
    }

    public function testPreUpdate()
    {
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());
        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->willReturn($this->token);
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->trick);

        $event = new TrickListener($this->tokenStorage);

        $event->preUpdate($this->args);

        $this->assertContainsOnlyInstancesOf(User::class, [$this->trick->getModifiedBy()]);
        $this->assertContainsOnlyInstancesOf(\Datetime::class, [$this->trick->getDateModified()]);
    }

    public function testPreUpdateWithInvalidTrick()
    {
        $this->args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity'])
            ->getMock();
        $this->args
        ->expects($this->once())
        ->method('getEntity')
        ->willReturn(new User());

        $event = new TrickListener($this->tokenStorage);

        $event->preUpdate($this->args);
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->token = null;
        $this->tokenStorage = null;
        $this->args = null;
        $this->trick = null;
    }
}
