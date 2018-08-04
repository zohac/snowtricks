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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session, \Twig_Environment $twig)
    {
        $this->flashBag = $session->getFlashBag();
        $this->twig = $twig;
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
            $template = $this->twig->load('Email/registration.twig');
            
            $this->flashBag->add(
                'info',
                $template->renderBlock('resend_email', ['token' => $user->getToken()])
            );
        }
    }
}
