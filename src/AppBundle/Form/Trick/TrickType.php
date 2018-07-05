<?php

// src/AppBundle/Form/Trick/AddType.php

namespace AppBundle\Form\Trick;

use AppBundle\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use AppBundle\Listener\AntiSqlInjectionFormListener;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Add or update a trick.
 */
class TrickType extends AbstractType
{
    /**
     * Build the form.
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
            ->add('title', TextType::class, [
                'constraints' => [new Regex([
                    'pattern' => '/^[a-zA-Z0-9\- ]+$/',
                    'message' => 'le nom du trick ne doit comporter que des caractères alphanumérique',
                ])],
            ])
            ->add('content', TextareaType::class)
            ->add('categories', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => 'AppBundle:Category',
                    'choice_label' => 'name',
                    'attr' => ['class' => 'custom-select col-xl-3 col-lg-4 col-md-5 col-8'],
                ],
                'allow_add' => true,
                'prototype' => true,
                'allow_delete' => true,
                'label' => false,
                'required' => true,
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => [
                    'attr' => ['class' => 'col-10'],
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'label' => false,
                'error_bubbling' => false,
            ])
            ->addEventSubscriber(new AntiSqlInjectionFormListener());
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
