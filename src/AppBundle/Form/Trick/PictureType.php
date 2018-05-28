<?php

// src/AppBundle/Form/Trick/PictureType.php

namespace AppBundle\Form\Trick;

use AppBundle\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Sub form for addType.
 */
class PictureType extends AbstractType
{
    /** 
     * Construct the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        // Using $option to avoid Codacy error "Unused Code"
        $option = null;

        // The entity fields are added to our form.
        $builder
            ->add('file', FileType::class, [
                'label' => 'Choisir une image',
                'label_attr' => ['class' => 'custom-file-label'],
                'required' => true,
                'constraints' => [new File()],
                'attr' => [
                    'class' => 'custom-file-input',
                    'lang' => 'fr',
                    'accept' => '.png, .jpg, .jpeg',
                ],
            ])
            ->add('headlinePicture', CheckboxType::class, [
                    'label' => 'Image mise en avant',
                    'label_attr' => ['class' => 'form-check-label'],
                    'required' => false,
                    'attr' => ['class' => ''],
                ]
            );
    }

    /**
     * The options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Picture::class,
        ));
    }
}
