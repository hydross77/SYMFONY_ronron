<?php

namespace App\Form;

use App\Entity\Cat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel est son nom ?'
            ])
            ->add('color', TextType::class, [
                'label' => 'De quelle couleur ?'
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Quel est son âge ?'
            ])
            ->add('breed', TextType::class, [
                'label' => 'De quelle race ?'
            ])
            ->add('tattoo', TextType::class, [
                'label' => 'A-t-il un tatouage ?'
            ])
            ->add('sterelized', ChoiceType::class, [
                'label' => 'Est-il stérilisé/castré ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('designCoat', TextType::class, [
                'label' => 'Comment est son pelage ?'
            ])
            ->add('lengthCoat', ChoiceType::class, [
                'label' => 'Taille de son pelage ?',
                'choices' => [
                    'Court' => 'court',
                    'Moyen' => 'moyen',
                    'Long' => 'long',
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Mâle' => 'male',
                    'Femelle' => 'femelle',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('chip', TextType::class, [
                'label' => 'Numéro de puce',
                'required' => false,
            ])
            ->add('picture', FileType::class, [
                'label' => 'Photo',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cat::class,
        ]);
    }
}
