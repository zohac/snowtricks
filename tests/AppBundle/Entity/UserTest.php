<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test the hydratation of the Entity and the relationship between entity.
     */
    public function testEntityUser()
    {
        $user = new User();
        $user->setUsername('zohac');
        $user->setEmail('zohac@test.com');
        $user->setPassword('aGreatPassword');
        $user->setPlainPassword('aGreatPassword');
        $user->setRoles(['ROLE_USER']);
        $user->setDateRegistration(new \Datetime('2018-03-08 22:38:53'));
        $user->setToken('aGreatToken');
        $user->setSalt('aGreatSalt');

        $avatar = new Picture();
        $user->setAvatar($avatar);

        $this->assertEquals('zohac', $user->getUsername());
        $this->assertEquals('zohac@test.com', $user->getEmail());
        $this->assertEquals('aGreatPassword', $user->getPassword());
        $this->assertEquals('aGreatPassword', $user->getPlainPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals(new \Datetime('2018-03-08 22:38:53'), $user->getDateRegistration());
        $this->assertEquals('aGreatToken', $user->getToken());
        $this->assertEquals('aGreatSalt', $user->getSalt());
        $this->assertEquals($avatar, $user->getAvatar());

        $this->assertNull($user->eraseCredentials());
    }

    /**
     * Test the id of the Entity.
     */
    public function testIdVideo()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneByUsername('john')
        ;

        $this->assertInternalType('int', $user->getId());
    }
}
