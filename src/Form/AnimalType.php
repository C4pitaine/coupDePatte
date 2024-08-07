<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Friandise;
use App\Entity\Indispensable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type')
            ->add('genre')
            ->add('age')
            ->add('race')
            ->add('description')
            ->add('adoptionDate', null, [
                'widget' => 'single_text',
            ])
            ->add('adopted')
            ->add('friandise', EntityType::class, [
                'class' => Friandise::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('indispensables', EntityType::class, [
                'class' => Indispensable::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
