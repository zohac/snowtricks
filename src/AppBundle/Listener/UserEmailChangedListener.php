<?php

namespace AppBundle\Listener;

use AppBundle\Events\UserEmailChangedEvent;

class UserEmailChangedListener
{
    public function onUserEmailChanged(UserEmailChangedEvent $event)
    {
        if ($event->hasEmailChanged()) {
            // Redirection to logout
            return;
        }
    }
}
