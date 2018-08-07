<?php

namespace tests\AppBundle\Utils\User;

use AppBundle\Entity\User;
use AppBundle\Utils\User\Registration;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class RegistrationTest extends TestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Twig_Environment
     */
    private $template;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var User
     */
    private $user;

    protected function setUp()
    {
        parent::setUp();

        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordEncoder = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface')
            ->disableOriginalConstructor()
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

        $this->user = new User();
        $this->user->setUsername('zohac');
        $this->user->setEmail('zohac@test.com');
        $this->user->setPassword('aGreatPassword');
        $this->user->setRoles([]);
        $this->user->setDateRegistration(new \Datetime('2018-06-23 10:46:00'));
        $this->user->setToken('aGreatToken');
    }

    public function testValidate()
    {
        $registration = new Registration(
            $this->entityManager,
            $this->session,
            $this->twig,
            $this->mailer
        );
        $registration->validate($this->user);

        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
        $this->assertNull($this->user->getToken());
    }

    public function testsendEmailForValidation()
    {
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(true);
        $this->template
            ->expects($this->exactly(3))
            ->method('renderBlock')
            ->willReturn('test');
        $this->twig
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->template);

        $registration = new Registration(
            $this->entityManager,
            $this->session,
            $this->twig,
            $this->mailer
        );
        $registration->sendEmailForValidation($this->user);
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->entityManager = null;
        $this->session = null;
    }
}
