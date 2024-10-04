<?php

namespace App\Form;

use App\Entity\Suivi;
use App\Form\ApplicationType;
use App\Repository\AnimalRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuiviType extends ApplicationType
{
    private $animalRepository;

    public function __construct(AnimalRepository $animalRepository)
    {
        $this->animalRepository = $animalRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $animals = $this->animalRepository->findAdopted();
        $choices = [];

       foreach($animals as $animal){
            $choices[$animal['name']] = $animal['name'];
        }    

        $builder
            ->add('image',FileType::class,$this->getConfiguration('Image','Une image de l\'animal adopté'))
            ->add('description',TextareaType::class,$this->getConfiguration('Description','Une description de comment se passe son séjour avec vous'))
            ->add('animal', ChoiceType::class,$this->getConfiguration('','',[
                'choices' => $choices
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}
