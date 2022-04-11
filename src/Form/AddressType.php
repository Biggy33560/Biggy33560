<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label'=>'Quel nom pour votre adresse ?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class,[
                'label'=>'Votre pénom',
                'attr'=>[
                    'placeholder'=>'Votre pénom'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label'=>'Votre nom',
                'attr'=>[
                    'placeholder'=>'Votre nom'
                ]
            ])
            ->add('company', TextType::class,[
                'label'=>'Votre société',
                'required'=>false,
                'attr'=>[
                    
                    'placeholder'=>'Votre société'
                ]
            ])
            ->add('address', TextType::class,[
                'label'=>'Quel nom pour votre adresse ?',
                'attr'=>[
                    'placeholder'=>'Nommez votre adresse'
                ]
            ])
            ->add('postal', TextType::class,[
                'label'=>'Votre Code Postal?',
                'attr'=>[
                    'placeholder'=>'Nommez votre CP'
                ]
            ])
            ->add('city', TextType::class,[
                'label'=>'Votre ville ?',
                'attr'=>[
                    'placeholder'=>'Nommez votre ville'
                ]
            ])
            ->add('country',CountryType ::class,[
                'label'=>' votre pays ?',
                'attr'=>[
                    'placeholder'=>'Nommez votre Pays'
                ]
            ])
            ->add('phone', TelType::class,[
                'label'=>'Votre Téléphone ?',
                'attr'=>[
                    'placeholder'=>'Votre Téléphone'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Valider',
                'attr'=>[
                    'class'=>'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
