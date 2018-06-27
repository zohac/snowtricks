<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AntiSqlInjection extends Constraint
{
    public $message =
        'The string "{{ string }}" contains an illegal word.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        // Ici, on fait appel à l'alias du service définit dans son tag
        return get_class($this).'Validator';
    }
}
