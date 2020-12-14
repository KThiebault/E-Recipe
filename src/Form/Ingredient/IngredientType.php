<?php

namespace App\Form\Ingredient;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Name: ",
                "required" => true,
            ])
            ->add("state", ChoiceType::class, [
                "choices" => $this->getChoices(),
                "label" => "State: ",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Ingredient::class,
        ]);
    }

    private function getChoices(): array
    {
        $output = [];
        foreach (Ingredient::STATE as $key => $value) {
            $output[$value] = $key;
        }
        return $output;
    }
}
