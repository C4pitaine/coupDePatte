<?php

namespace App\Form;

use App\Entity\Adoption;
use App\Entity\Indispensable;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AdoptionOneType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,$this->getConfiguration('Nom','Le nom de votre animal'))
            ->add('type',ChoiceType::class,$this->getConfiguration('Animal','Le type d\'animal',[
                'choices' => [
                    'Chien' => 'chien',
                    'Chat' => 'chat',
                    'Lapin' => 'lapin'
                ]
            ]))
            ->add('indispensables', EntityType::class, [
                'class' => Indispensable::class,
                'choice_label' => 'title',
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('genre',ChoiceType::class,$this->getConfiguration('Genre','Le genre de votre animal',[
                'choices' => [
                    'Mâle' => 'male',
                    'Femelle' => 'femelle'
                ]
            ]))
            ->add('age',NumberType::class,$this->getConfiguration('Âge','L\'âge de votre animal'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adoption::class,
        ]);
    }
}
