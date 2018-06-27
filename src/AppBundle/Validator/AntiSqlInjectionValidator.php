<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntiSqlInjectionValidator extends ConstraintValidator
{
    private $sqlCommand = [
        'UPDATE',
        'DELETE',
        'SELECT',
    ];

    public function validate($value, Constraint $constraint)
    {
        //dump($constraint);
        //dump($value); die;
        foreach ($this->sqlCommand as $command) {
            $value = str_ireplace($command, '', $value);
            /*
            if ($value = str_ireplace($command, '', $value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }*/
        }
    }
}
