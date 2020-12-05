<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SamePassword extends Constraint
{
    public string $message = "The current password is not the same.";
}
