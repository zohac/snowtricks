<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test the hydratation of the Entity and the relationship between entity.
     */
    public function testEntityUser()
    {
        $user = new User();
        $user->setUsername('zohac');
        $user->setEmail('zohac@test.com');
        $user->setEmailRecovery('zohac@test.com');
        $user->setPassword('aGreatPassword');
        $user->setPlainPassword('aGreatPassword');
        $user->setRoles(['ROLE_USER']);
        $user->setDateRegistration(new \Datetime('2018-03-08 22:38:53'));
        $user->setToken('aGreatToken');

        $avatar = new Picture();
        $user->setAvatar($avatar);

        $this->assertEquals('zohac', $user->getUsername());
        $this->assertEquals('zohac@test.com', $user->getEmail());
        $this->assertEquals('zohac@test.com', $user->getEmailRecovery());
        $this->assertEquals('aGreatPassword', $user->getPassword());
        $this->assertEquals('aGreatPassword', $user->getPlainPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals(new \Datetime('2018-03-08 22:38:53'), $user->getDateRegistration());
        $this->assertEquals('aGreatToken', $user->getToken());
        $this->assertEquals($avatar, $user->getAvatar());
    }
}
