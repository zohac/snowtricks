<?php

// src/AppBundle/Form/Comment/AddType.php

namespace AppBundle\Form\Comment;

use AppBundle\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Add or update a comment.
 */
class CommentType extends AbstractType
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
            ->add('content', TextareaType::class)
        ;
    }

    /**
     * The option.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class,
        ));
    }
}
