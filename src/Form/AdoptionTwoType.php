<?php

namespace App\Form;

use App\Entity\Adoption;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdoptionTwoType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Race',TextType::class,$this->getConfiguration('Race','La race de votre animal'))
            ->add('image',FileType::class,$this->getConfiguration('Image','Une image de votre animal'))
            ->add('description',TextareaType::class,$this->getConfiguration('Description','Décrivez son caractère et sa façon d\'être'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adoption::class,
        ]);
    }
}
