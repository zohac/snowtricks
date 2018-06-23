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
        $this->comment = new Comment();

        $token = $this
            ->getMockBuilder(TokenInterface::class)
            ->getMock();
        $token
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
            ->willReturn($this->returnValue($token));
        $this->tokenStorage
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());

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

    public function testSecurityInteractiveLogin()
    {
        $event = new CommentListener($this->tokenStorage);

        $event->onAddCommentEvent($this->commentEvent);
        $event->prePersist($this->args);
    }
}
