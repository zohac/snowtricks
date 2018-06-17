<?php

// src/AppBundle/Form/ResetPasswordType.php

namespace AppBundle\Form\User;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * The form for changing the password.
 */
class ResetPasswordType extends AbstractType
{
    /**
     * Creating the User reset password Form.
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'constraints' => [
                    new Length(['max' => 4096]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9]{6,}$/',
                        'message' => 'Le mot de passe doit comporter au moins 6 caractères,
                        minuscule, majuscule et numérique.',
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
