<?php

namespace App\DataTransferObject\Account;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePassword
{
    /**
     * @Assert\NotBlank
     * @App\Validator\SamePassword
     */
    private string $password;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="6", max="4096")
     */
    private string $plainPassword;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UpdatePassword
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): UpdatePassword
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
}
