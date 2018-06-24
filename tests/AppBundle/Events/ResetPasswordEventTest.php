<?php

namespace tests\AppBundle\Events;

use AppBundle\Entity\User;
use AppBundle\Events\ResetPasswordEvent;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class ResetPasswordEventTest extends TestCase
{
    public function testEvents()
    {
        $event = new ResetPasswordEvent(new User());

        $this->assertContainsOnlyInstancesOf(User::class, [$event->getUser()]);
    }
}
