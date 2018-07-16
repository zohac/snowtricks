<?php

namespace AppBundle\Utils\Trick;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Picture;
use AppBundle\Utils\Uploader;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrickTypeHandler
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * Constructor.
     *
     * @param ObjectManager $entityManager
     */
    public function __construct(
        ObjectManager $entityManager,
        TokenStorageInterface $tokenStorage,
        Uploader $uploader
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->uploader = $uploader;
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
           //dump($form->getData()); die;
            $data = $form->getData();

            $trick = new Trick();

            $trick->setTitle($data['title']);
            $trick->setContent($data['content']);

            // Set the authenticated user
            $trick->setUser($this->tokenStorage->getToken()->getUser());

            if (is_array($data['categories'])) {
                foreach ($data['categories'] as $category) {
                    $trick->addCategory($category);
                }
            }

            if (is_array($data['pictures'])) {
                foreach ($data['pictures'] as $image) {
                    $this->uploader->setPath(Uploader::UPLOAD_PICTURE_DIR);
                    $this->uploader->uploadFile($image['file']);

                    $picture = new Picture();
                    $picture->setName($this->uploader->getName());
                    $picture->setPath($this->uploader->getPath().'/'.$this->uploader->getName());
                    $picture->setHeadLinePicture($image['headlinePicture']);

                    $trick->addPicture($picture);
                }
            }

            if (is_array($data['videos'])) {
                foreach ($data['videos'] as $video) {
                    $trick->addCategorie($video);
                }
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
