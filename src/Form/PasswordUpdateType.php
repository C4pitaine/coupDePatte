<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Mot de passe actuel","Mot de passe actuel",['toggle'=>true]))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe","Votre nouveau mot de passe ",['toggle'=>true]))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe","Confirmation de votre mot de passe",['toggle'=>true]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
