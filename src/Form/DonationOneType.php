<?php

namespace App\Form;

use App\Entity\Donation;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationOneType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant',NumberType::class,$this->getConfiguration('Montant','Montant de votre donation'))
            ->add('email',EmailType::class,$this->getConfiguration('Adresse e-mail','Votre adresse e-mail'))
            ->add('lastName',TextType::class,$this->getConfiguration('Nom','Votre nom'))
            ->add('firstName',TextType::class,$this->getConfiguration('Prénom','Votre prénom'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
