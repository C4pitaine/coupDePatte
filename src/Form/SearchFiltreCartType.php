<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFiltreCartType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search',TextType::class,$this->getConfiguration('','Rechercher',['required'=>false]))
            ->add('filtre',ChoiceType::class,$this->getConfiguration('','Filtre',[
                'choices' => [
                    "" => "",
                    "Payé" => 'payé',
                    "En attente " => 'en attente',
                    "Annulé " => 'annulé',
                ],
                'required' => false,
            ]));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
