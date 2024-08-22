<?php

namespace App\Form;

use App\Entity\Cart;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,$this->getConfiguration('Nom','Votre nom'))
            ->add('firstName',TextType::class,$this->getConfiguration('Prénom','Votre prénom'))
            ->add('email',EmailType::class,$this->getConfiguration('Email','Votre addresse email'))
            ->add('cart',TextareaType::class,$this->getConfiguration('Panier',''))
            ->add('total',NumberType::class,$this->getConfiguration('Total',''))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}
