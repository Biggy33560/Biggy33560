<?php

namespace App\Form;

use App\Entity\Search;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        //affichage du formulaire et changement des label
        ->add('string', TextType::class, [
            'label'=>false,
            'required'=>false,
            'attr'=>[
                'placeholder'=>'Votre recherche...',
                'class'=>'form-control-sm'
                ]
            ])
        ->add('categories',EntityType::class,[
            'label'=>false,
            'required'=>false,
            'class'=>Category::class,
            'multiple'=>true,
            'expanded'=>true


        ])

        ->add('submit',SubmitType::class,[
            'label'=>'Filtrer',
            'attr'=>[
                'class'=>'btn-block btn-primary'
                ]


        ]);

    
    
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}