<?php

namespace App\Form;

use App\Entity\Cat;
use App\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// l'extension permet de faire savoir a Symfony que la class est un formulaire
class SearchForm2 extends SearchForm
{

    //construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Perdu/Retrouvé :',
                'required' => false,
                'choices' => [
                    'Perdu' => 'Perdu',
                    'Retrouvé' => 'Retrouvé',
                ],
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
                    'American Bobtail' => 'American Bobtail',
                    'American Curl' => 'American Curl',
                    'American Shorthair' => 'American Shorthair',
                    'American Wirehair' => 'American Wirehair',
                    'Balinese' => 'Balinese',
                    'Bengal' => 'Bengal',
                    'Birman' => 'Birman',
                    'Bombay' => 'Bombay',
                    'British Shorthair' => 'British Shorthair',
                    'Burmese' => 'Burmese',
                    'Burmilla' => 'Burmilla',
                    'Chartreux' => 'Chartreux',
                    'Chausie' => 'Chausie',
                    'Colorpoint Shorthair' => 'Colorpoint Shorthair',
                    'Cornish Rex' => 'Cornish Rex',
                    'Cymric' => 'Cymric',
                    'Devon Rex' => 'Devon Rex',
                    'Don Sphynx' => 'Don Sphynx',
                    'Egyptian Mau' => 'Egyptian Mau',
                    'European Shorthair' => 'European Shorthair',
                    'Exotic Shorthair' => 'Exotic Shorthair',
                    'Havana Brown' => 'Havana Brown',
                    'Himalayan' => 'Himalayan',
                    'Japanese Bobtail' => 'Japanese Bobtail',
                    'Javanese' => 'Javanese',
                    'Korat' => 'Korat',
                    'LaPerm' => 'LaPerm',
                    'Maine Coon' => 'Maine Coon',
                    'Manx' => 'Manx',
                    'Munchkin' => 'Munchkin',
                    'Nebelung' => 'Nebelung',
                    'Norwegian Forest' => 'Norwegian Forest',
                    'Ocicat' => 'Ocicat',
                    'Oriental' => 'Oriental',
                    'Persian' => 'Persian',
                    'Peterbald' => 'Peterbald',
                    'Pixie-bob' => 'Pixie-bob',
                    'Ragdoll' => 'Ragdoll',
                    'Russian Blue' => 'Russian Blue',
                    'Savannah' => 'Savannah',
                    'Scottish Fold' => 'Scottish Fold',
                    'Selkirk Rex' => 'Selkirk Rex',
                    'Siamese' => 'Siamese',
                    'Siberian' => 'Siberian',
                    'Singapura' => 'Singapura',
                    'Snowshoe' => 'Snowshoe',
                    'Sokoke' => 'Sokoke',
                    'Somali' => 'Somali',
                    'Sphynx' => 'Sphynx',
                    'Tonkinese' => 'Tonkinese',
                    'Turkish Angora' => 'Turkish Angora',
                    'Turkish Van' => 'Turkish Van'

                ],
            ])
            ->add('city', TextType::class, [
                'required'=>false,
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
                'choice_value' => function (?Color $color) {
                    return $color ? $color->getId() : '';
                },
                'placeholder' => 'Choisir une couleur',
                'by_reference' => false, // Ajoutez cette ligne
            ]);

    }

    //configuration du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'POST'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}