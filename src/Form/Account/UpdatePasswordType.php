<?php

namespace App\Form\Account;

use App\DataTransferObject\Account\UpdatePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("password", PasswordType::class, [
                "label" => "Current password: ",
                "required" => true,
            ])
            ->add("plainPassword", RepeatedType::class, [
                "type" => PasswordType::class,
                "first_options" => ["label" => "New password: "],
                "second_options" => ["label" => "Repeat new password: "],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => UpdatePassword::class,
        ]);
    }
}
