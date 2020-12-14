<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 * @UniqueEntity("name", message="This ingredient already exist", repositoryMethod="uniqueName")
 */
class Ingredient
{
    public const STATE = [
        0 => "Liquid",
        1 => "Solid"
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private ?string $name;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     * @Assert\NotBlank()
     * @Assert\Choice({0, 1})
     */
    private ?int $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ingredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): Ingredient
    {
        $this->name = $name;
        return $this;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(?int $state): Ingredient
    {
        $this->state = $state;
        return $this;
    }

    public function getStateType(): string
    {
        return self::STATE[$this->state];
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Ingredient
    {
        $this->user = $user;
        return $this;
    }
}
