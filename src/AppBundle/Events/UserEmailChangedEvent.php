<?php

namespace AppBundle\Events;

use Symfony\Component\EventDispatcher\Event;

class UserEmailChangedEvent extends Event
{
    const NAME = 'user.emailChanged';

    /**
     * @var bool
     */
    private $emailChanged;

    public function __construct()
    {
        $this->emailChanged = true;
    }

    /**
     * Get the value of emailHasChanged.
     *
     * @return bool
     */
    public function hasEmailChanged(): bool
    {
        return $this->emailChanged;
    }
}
