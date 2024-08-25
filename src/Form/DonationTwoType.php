<?php

namespace App\Form;

use App\Entity\Donation;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DonationTwoType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse',TextType::class,$this->getConfiguration('Adresse','Votre adresse'))
            ->add('codePostal',NumberType::class,$this->getConfiguration('Code postal','Votre code postal'))
            ->add('ville',TextType::class,$this->getConfiguration('Ville','Votre ville'))
            ->add('pays',TextType::class,$this->getConfiguration('Pays','Votre pays'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
