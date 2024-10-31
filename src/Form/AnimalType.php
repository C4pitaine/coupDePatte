<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Friandise;
use App\Entity\Indispensable;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnimalType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,$this->getConfiguration("Nom","Le nom de l'animal"))
            ->add('type',ChoiceType::class,$this->getConfiguration("Animal",'',[
                'choices' => [
                    'Chat' => "chat",
                    'Chien' => "chien",
                    'Lapin' => "lapin"
                ]
            ]))
            ->add('genre',ChoiceType::class,$this->getConfiguration("Genre",'',[
                'choices'=>[
                    "Mâle" => "male",
                    "Femelle" => "female"
                ]
            ]))
            ->add('age',NumberType::class,$this->getConfiguration("Âge","L'âge de l'animal"))
            ->add('race',TextType::class,$this->getConfiguration("Race","La race de l'animal"))
            ->add('description',TextareaType::class,$this->getConfiguration("Description","La description de l'animal"))
            ->add('friandise', EntityType::class, [
                'class' => Friandise::class,
                'choice_label' => function (Friandise $friandise): string{
                    return $friandise->getName() .' - '. $friandise->getAnimal();
                },
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('indispensables', EntityType::class, [
                'class' => Indispensable::class,
                'choice_label' => 'title',
                'multiple' => true,
                'autocomplete' => true,
            ])
            // ->add('images', CollectionType::class, [
            //     'entry_type' => ImageType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true
            // ])
            ->add('coverImage',FileType::class,$this->getConfiguration("Image","L'image de votre animal"))
            ->add('adopted',ChoiceType::class,$this->getConfiguration("Adopté",'',[
                'choices'=>[
                    "Non" => false,
                    "Oui" => true
                ]
            ]))
            ->add('adoptionDate',DateType::class, $this->getConfiguration("Date d'adoption","Date d'adoption",[
                'widget' => 'single_text',
                'required' => false,
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
