<?php

namespace AppBundle\Listener;

use AppBundle\Utils\Menu;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MenuListener
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Menu
     */
    protected $menu;

    /**
     * @param Menu $menu
     */
    public function __construct(\Twig_Environment $twig, Menu $menu)
    {
        $this->twig = $twig;
        $this->menu = $menu;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        // Get the route
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Get the menu
        $menu = $this->menu->get();

        // Add the menu to the view
        $this->twig->addGlobal('menu', $menu);
        $this->twig->addGlobal('route', $route);
    }
}
