<?php

// src/AppBundle/Form/ForgotPasswordType.php

namespace AppBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * The form when you lose your password.
 */
class ForgotPasswordType extends AbstractType
{
    /**
     * Creating the User forget password Form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        // Using $option to avoid Codacy error "Unused Code"
        $option = null;

        // The entity fields are added to our form.
        $builder->add('emailRecovery', EmailType::class, [
            'required' => true,
            'constraints' => [
                new Email(),
                new NotBlank(),
            ],
        ]);
    }
}
