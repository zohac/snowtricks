<?php

namespace tests\AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use AppBundle\Events\AddCommentEvent;
use AppBundle\Listener\CommentListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentListenerTest extends TestCase
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
     * @var Comment
     */
    private $comment;

    /**
     * @var AddCommentEvent
     */
    private $commentEvent;

    protected function setUp()
    {
        parent::setUp();

        $this->comment = new Comment();

        $this->token = $this
            ->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMockForAbstractClass();
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());

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
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->comment);

        $this->commentEvent = new AddCommentEvent(new Trick());
    }

    public function testPrePersist()
    {
        $event = new CommentListener($this->tokenStorage);

        $event->onAddCommentEvent($this->commentEvent);
        $event->prePersist($this->args);

        $this->assertContainsOnlyInstancesOf(Trick::class, [$this->comment->getTrick()]);
        $this->assertContainsOnlyInstancesOf(User::class, [$this->comment->getUser()]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->token = null;
        $this->tokenStorage = null;
        $this->args = null;
        $this->comment = null;
        $this->commentEvent = null;
    }
}
