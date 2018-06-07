<?php

// src/AppBundle/Form/Comment/AddType.php

namespace AppBundle\Form\Comment;

use AppBundle\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            ->add('content', TextareaType::class, [
                'constraints' => [new Type([
                    'type' => 'string',
                    'message' => 'The value {{ value }} is not a valid {{ type }}.',
                ])],
            ]);
    }
}
