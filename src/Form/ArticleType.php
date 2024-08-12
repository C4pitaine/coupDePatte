<?php

namespace App\Form;

use App\Entity\Article;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration('Titre','Titre de l\'article'))
            ->add('image', FileType::class,$this->getConfiguration('Image','Image de l\'article'))
            ->add('link',UrlType::class,$this->getConfiguration('Lien','Url de l\'article'))
            ->add('type',ChoiceType::class,$this->getConfiguration('Type',"Le type d'article",[
                'choices' => [
                    "" => "",
                    "Nutrition" => "nutrition",
                    "Actualité" => "actualite",
                    "Bien-être" => "bien_etre",
                    "Santé" => "sante",
                ]
            ]))
            ->add('text',TextareaType::class,$this->getConfiguration('Description','Description de l\'article',['required'=>false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
