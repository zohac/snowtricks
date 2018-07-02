<?php

namespace tests\AppBundle\Events;

use AppBundle\Entity\Trick;
use AppBundle\Events\AddCommentEvent;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class AddCommentEventTest extends TestCase
{
    public function testEvents()
    {
        $event = new AddCommentEvent(new Trick());

        $this->assertContainsOnlyInstancesOf(Trick::class, [$event->getTrick()]);
    }
}
