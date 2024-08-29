<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,$this->getConfiguration('Adresse E-mail','Votre adresse e-mail'))
            ->add('lastName',TextType::class,$this->getConfiguration('Nom','Votre nom'))
            ->add('firstName',TextType::class,$this->getConfiguration('Prénom','Votre prénom'))
            ->add('password',PasswordType::class,$this->getConfiguration('Mot de passe','Mot de passe',['toggle'=>true]))
            ->add('passwordConfirm',PasswordType::class,$this->getConfiguration('Confirmation','Confirmer votre mot de passe',['toggle'=>true]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
