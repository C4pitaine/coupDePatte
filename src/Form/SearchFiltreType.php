<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchFiltreType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $options['choices'];

        $builder
            ->add('search',TextType::class,$this->getConfiguration('','Rechercher',['required'=>false]))
            ->add('filtre',ChoiceType::class,$this->getConfiguration('','Filtre',[
                'choices' => $choices,
                'required' => false,
            ]));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
        $resolver->setDefined('choices');
    }
}
