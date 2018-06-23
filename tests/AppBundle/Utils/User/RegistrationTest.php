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

    protected function setUp()
    {
        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->session = new Session(new MockArraySessionStorage());
    }

    public function testValidate()
    {
        $user = new User();
        $user->setUsername('zohac');
        $user->setEmail('zohac@test.com');
        $user->setPassword('aGreatPassword');
        $user->setRoles([]);
        $user->setDateRegistration(new \Datetime('2018-06-23 10:46:00'));
        $user->setToken('aGreatToken');

        $registration = new Registration($this->entityManager, $this->session);
        $registration->validate($user);

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertNull($user->getToken());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->entityManager = null;
        $this->session = null;
    }
}
