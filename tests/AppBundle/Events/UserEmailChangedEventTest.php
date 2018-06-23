<?php

namespace tests\AppBundle\Events;

use AppBundle\Events\UserEmailChangedEvent;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class UserEmailChangedEventTest extends TestCase
{
    public function testEvents()
    {
        $event = new UserEmailChangedEvent();

        $this->assertTrue($event->hasEmailChanged());
    }
}
