<?php

// src/AppBundle/Service/Menu/menu.php

namespace AppBundle\Utils;

use Symfony\Component\Routing\RouterInterface;

/**
 * The menu is built from the annotations in the routes through the route options.
 *
 * exemple:
 * //@Route("/path/to/the/trick", name="ST_name_of_the_route",
 *      options={"menu": {
 *          "id": "an_id",
 *          "name": "A_good_name",
 *          "order": 1 //optional
 *      }})
 */
class Menu
{
    /**
     * The menu.
     *
     * @var array
     */
    private $menu = [];

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        // If the order is not defined, it is fixed arbitrarily
        $undefinedOrder = 100;

        // For each route defined
        foreach ($router->getRouteCollection()->all() as $path => $route) {
            //If the route has "menu" option
            if ($menu = $route->getOption('menu')) {
                // If the order is not defined, it is fixed arbitrarily
                if (!array_key_exists('order', $menu)) {
                    $menu['order'] = $undefinedOrder++;
                }
                // We build the menu
                $this->menu[$menu['id']][$menu['order']] = [
                    'name' => $menu['name'],
                    'path' => $path,
                ];
            }
        }
        foreach ($this->menu as $key => $value) {
            ksort($value);
            // Reorganize the menu
            $menu[$key] = $value;
        }
        $this->menu = $menu;
    }

    /**
     * Get the menu.
     *
     * @param string $id
     *
     * @return array
     */
    public function get(): array
    {
        // Return the menu
        return $this->menu;
    }
}
