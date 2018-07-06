<?php

namespace AppBundle\Utils\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ForgotPasswordTypeHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param ObjectManager     $entityManager
     * @param SessionInterface  $session
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer     $mailer
     */
    public function __construct(
        ObjectManager $entityManager,
        SessionInterface $session,
        \Twig_Environment $twig,
        \Swift_Mailer $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $session->getFlashBag();
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    /**
     * Handle a form.
     *
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handle(FormInterface $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            // 1) Get the user
            $data = $form->getData();
            $user = $this->entityManager->getRepository(User::class)->getUserWithEmail($data['emailRecovery']);

            // 2) If the user exist
            if ($user) {
                // 2.1) Set token
                $user->setToken(hash('sha256', serialize($user).microtime()));

                // 2.2) Send an email
                $template = $this->twig->load('Email/reset_password.twig');
                $mail = (new \Swift_Message())
                    // Give the message a subject
                    ->setSubject($template->renderBlock('subject', []))
                    ->setBody($template->renderBlock('body_text', ['token' => $user->getToken()]), 'text/plain')
                    // And optionally an alternative body
                    ->addPart($template->renderBlock('body_html', ['token' => $user->getToken()]), 'text/html')
                    ->setTo($user->getEmail())
                    ->setFrom('contact@snowtricks.com')
                ;

                if (!$this->mailer->send($mail)) {
                    $this->flashBag->add(
                        'danger',
                        'Un email de confirmation n\'a pu vous être envoyé.
                        Connectez vous à votre compte et vérifié votre adresse mail.
                        Tant que votre adresse email ne seras pas vérifié,
                        vous ne pourrez pas poster de Trick ou des commentaires.'
                    );
                }

                // 2.3) save the User!
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return true;
            }
            $this->flashBag->add('danger', 'Aucun utilisateur avec cette adresse email.');
        }

        return false;
    }
}
