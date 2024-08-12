<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFiltreArticleType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search',TextType::class,$this->getConfiguration('','Rechercher',['required'=>false]))
            ->add('filtre',ChoiceType::class,$this->getConfiguration('','Filtre',[
                'choices' => [
                    "" => "",
                    "Nutrition" => "nutrition",
                    "Actualité" => "actualite",
                    "Bien-être" => "bien_etre",
                    "Santé" => "sante",
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