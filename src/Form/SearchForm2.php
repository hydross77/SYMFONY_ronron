<?php

namespace App\Form;

use App\Entity\Cat;
use App\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// l'extension permet de faire savoir a Symfony que la class est un formulaire
class SearchForm2 extends AbstractType
{

    //construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET') // Spécifie la méthode GET pour le formulaire
            ->add('type', ChoiceType::class, [
                'label' => 'Perdu/Retrouvé :',
                'required' => false,
                'choices' => [
                    'Perdu' => 'Perdu',
                    'Retrouvé' => 'Retrouvé',
                ],
            ])
            ->add('date_cat', DateType::class, [
                'label' => 'A partir de :',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'datepicker'],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Mâle' => 'Mâle',
                    'Femelle' => 'Femelle',
                    'Inconnu' => 'Inconnu',
                ],
            ])
            ->add('length_coat', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Court' => 'Court',
                    'Long' => 'Long',
                    'Ne sais pas' => 'Ne sais pas',
                ],
            ])
            ->add('design_coat', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Uni' => 'Uni',
                    'Tacheté' => 'Tacheté',
                    'Tigré' => 'Tigré',
                    'Ne sais pas' => 'Ne sais pas',
                ],
            ])
            ->add('breed', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Aucune' => 'Aucune',
                    'Abyssin' => 'Abyssin',
                ],
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['class' => 'city'],
            ])
            ->add('color', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Color::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Choisir une couleur',
                'by_reference' => false,
            ]);
    }

    // Configuration du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false, // Désactive la protection CSRF pour les requêtes GET
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // Retourne une chaîne vide pour éviter le préfixe du nom du formulaire
    }
}