<?php

// src/AppBundle/Form/User/AvatarType.php

namespace AppBundle\Form\User;

use AppBundle\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Sub form for UpdateType.
 */
class AvatarType extends AbstractType
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
                'label' => false,
                'required' => false,
                'constraints' => [new File()],
            ]);
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
