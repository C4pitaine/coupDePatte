<?php

namespace App\Form;

use App\Form\ApplicationType;
use App\Repository\AnimalRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFiltreAnimalType extends ApplicationType
{
    
    private $animalRepository;

    public function __construct(AnimalRepository $animalRepository)
    {
        $this->animalRepository = $animalRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       $type = $options['type'];
       $races = $this->animalRepository->findRaces($type);

       $choices = [];

       foreach($races as $race){
            $choices[$race['race']] = $race['race'];
       }

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
        $resolver->setDefined('type');
    }
}
