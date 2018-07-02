<?php

namespace AppBundle\Events;

use AppBundle\Entity\Trick;
use Symfony\Component\EventDispatcher\Event;

class AddCommentEvent extends Event
{
    const NAME = 'comment.add';

    /**
     * @var Trick
     */
    private $trick;

    /**
     * Constructor.
     *
     * @param Trick $trick
     */
    public function __construct(Trick $trick)
    {
        $this->trick = $trick;
    }

    /**
     * Get the entity Trick.
     *
     * @return Trick
     */
    public function getTrick(): Trick
    {
        return $this->trick;
    }
}
