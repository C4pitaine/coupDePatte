<?php

namespace App\Form;

use App\Entity\Parrainage;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParrainageType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant',NumberType::class,$this->getConfiguration('Montant','Le montant de votre parrainage'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parrainage::class,
        ]);
    }
}
