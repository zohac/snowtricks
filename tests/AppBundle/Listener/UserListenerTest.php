<?php

namespace tests\AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use AppBundle\Listener\UserListener;
use AppBundle\Events\ResetPasswordEvent;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class UserListenerTest extends TestCase
{
    /**
     * @var LifecycleEventArgs
     */
    private $args;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Twig_Environment
     */
    private $template;

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User();

        $this->args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity'])
            ->getMock();

        $this->session = new Session(new MockArraySessionStorage());

        $this->mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->template = $this
            ->getMockBuilder(\Twig_Template::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderBlock'])
            ->getMockForAbstractClass();
        $this->twig = $this
            ->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testPrePersist()
    {
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->user);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->prePersist($this->args);

        $this->assertRegExp('#^[0-9a-f]{64}$#', $this->user->getToken());
        $this->assertEmpty($this->user->getRoles());
        $this->assertContainsOnlyInstancesOf(\Datetime::class, [$this->user->getDateRegistration()]);
        $this->assertContainsOnlyInstancesOf(Picture::class, [$this->user->getAvatar()]);
    }

    public function testPrePersistWithInvalidUser()
    {
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn(new Picture());

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->prePersist($this->args);
    }

    public function testPostPersist()
    {
        $this->user->setEmail('email@test.com');

        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->user);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->postPersist($this->args);
    }

    public function testPostPersistWithInvalidUser()
    {
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn(new Picture());

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->postPersist($this->args);
    }

    public function testPostPersistWithNoSend()
    {
        $this->user->setEmail('email@test.com');

        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->user);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(false);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->postPersist($this->args);
    }

    public function testPreUpdate()
    {
        $this->user->setEmail('email@test.com');

        $this->args = $this
            ->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity', 'hasChangedField'])
            ->getMock();
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->user);
        $this->args
            ->expects($this->once())
            ->method('hasChangedField')
            ->willReturn(true);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->preUpdate($this->args);

        $this->assertRegExp('#^[0-9a-f]{64}$#', $this->user->getToken());
        $this->assertEmpty($this->user->getRoles());
    }

    public function testPreUpdateWithNoSend()
    {
        $this->user->setEmail('email@test.com');

        $this->args = $this
            ->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity', 'hasChangedField'])
            ->getMock();
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($this->user);
        $this->args
            ->expects($this->once())
            ->method('hasChangedField')
            ->willReturn(true);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(false);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->preUpdate($this->args);

        $this->assertRegExp('#^[0-9a-f]{64}$#', $this->user->getToken());
        $this->assertEmpty($this->user->getRoles());
    }

    public function testPreUpdateWithInvalidUser()
    {
        $this->user->setEmail('email@test.com');

        $this->args = $this
            ->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEntity'])
            ->getMock();
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn(new Picture());

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->preUpdate($this->args);
    }

    public function testOnResetPassword()
    {
        $this->user->setEmail('email@test.com');

        $this->args = $this
            ->getMockBuilder(ResetPasswordEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        $this->args
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->onResetPassword($this->args);

        $this->assertRegExp('#^[0-9a-f]{64}$#', $this->user->getToken());
    }

    public function testOnResetPasswordWithNoSend()
    {
        $this->user->setEmail('email@test.com');

        $this->args = $this
            ->getMockBuilder(ResetPasswordEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        $this->args
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(false);
        $this->template
            ->expects($this->any())
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $event = new UserListener($this->session, $this->twig, $this->mailer);

        $event->onResetPassword($this->args);

        $this->assertRegExp('#^[0-9a-f]{64}$#', $this->user->getToken());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->args = null;
        $this->user = null;
        $this->session = null;
        $this->mailer = null;
        $this->twig = null;
        $this->template = null;
    }
}
