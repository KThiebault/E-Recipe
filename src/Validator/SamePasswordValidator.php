<?php

namespace App\Validator;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SamePasswordValidator extends ConstraintValidator
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private Security $security;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Security $security)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SamePassword) {
            throw new UnexpectedTypeException($constraint, SamePassword::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (null === $user = $this->security->getUser()) {
            throw new \LogicException("User cannot be null.");
        }

        if (!$this->passwordEncoder->isPasswordValid($user, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
