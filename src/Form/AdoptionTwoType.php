<?php

namespace App\Form;

use App\Entity\Adoption;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('race',TextType::class,$this->getConfiguration('Race','La race de votre animal'))
            ->add('image',FileType::class,$this->getConfiguration('Image','Une image de votre animal'))
            ->add('description',TextareaType::class,$this->getConfiguration('Description','Décrivez son caractère et sa façon d\'être'))
            ->add('email',EmailType::class,$this->getConfiguration('Adresse e-mail','Votre adresse email'))
            ->add('tel',TextType::class,$this->getConfiguration('Numéro de téléphone','Votre numéro de téléphone'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adoption::class,
        ]);
    }
}
