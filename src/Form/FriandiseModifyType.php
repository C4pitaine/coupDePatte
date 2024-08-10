<?php

namespace App\Form;

use App\Entity\Friandise;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class FriandiseModifyType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',TextType::class,$this->getConfiguration('Nom','Le nom de la friandise'))
        ->add('price',NumberType::class,$this->getConfiguration('Prix','Le prix de la friandise'))
        ->add('image',FileType::class,$this->getConfiguration('Image',"L'image de la friandise",['required'=>false]))
        ->add('animal',ChoiceType::class,$this->getConfiguration('Animal',"",[
            'choices' => [
                'Chat' => "chat",
                'Chien' => "chien",
                'Lapin' => "lapin"
            ]
        ]))
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Friandise::class,
        ]);
    }
}
