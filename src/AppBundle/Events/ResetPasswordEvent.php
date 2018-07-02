<?php

namespace AppBundle\Events;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class ResetPasswordEvent extends Event
{
    const NAME = 'user.resetPassword';

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
