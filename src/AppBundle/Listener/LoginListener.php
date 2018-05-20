<?php

namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Custom login listener.
 */
class LoginListener
{
    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Do the magic.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (empty($user->getRoles())) {
            $this->flashBag->add(
                'login_listener',
                [
                    'type' => 'info',
                    'title' => 'Votre compte n\'est pas validé!',
                    'message' => 'Voulez vous que l\'on vous renvoie un mail de confirmation?',
                ]
            );
        }
    }
}
