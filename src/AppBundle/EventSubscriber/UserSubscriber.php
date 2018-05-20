<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Controller\UserSubscripterController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var User
     */
    private $user;

    /**
     * @var FlashBag
     */
    private $flashBag;

    public function __construct(
        ObjectManager $entityManager,
        RouterInterface  $router,
        SessionInterface $session
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $session->getFlashBag();
        $this->user = new User();
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof UserSubscripterController) {
            $forgot_password = $event->getRequest()->request->get('forgot_password');

            if (filter_var($forgot_password['emailRecovery'], FILTER_VALIDATE_EMAIL)) {
                $user = $this->entityManager
                    ->getRepository(User::class)
                    ->getUserWithemailRecovery($forgot_password['emailRecovery']);

                if (empty($user)) {
                    $this->flashBag->add(
                        'update_user',
                        [
                           'type' => 'info',
                           'title' => 'Aucun utilisateur avec cette adresse email.',
                           'message' => 'Vérifié votre adresse mail ou inscrivez-vous.',
                        ]
                    );

                    return new RedirectResponse($this->router->generate('ST_forgot_password'));
                }
                $this->user = $user;
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }

    public function getUser()
    {
        return $this->user;
    }
}
