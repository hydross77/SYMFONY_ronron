<?php

namespace App\Form;

use App\Entity\Cat;
use App\Entity\Color;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel est son nom :',
                'required' => false,
                'attr' => [
                    'maxlength' => 20, // Définir une longueur maximale pour éviter les attaques par débordement de tampon
                ],
            ])
            ->add('colors', EntityType::class, [
                'label' => 'Couleur(s) :',
                'required' => true,
                'mapped' => false,
                'class' => Color::class,
                'choice_label' => 'name',
                'multiple' => true, // Définit que le champ permet un choix multiple
                'expanded' => true, // Définit que le champ est affiché sous forme de boutons (si vous souhaitez des cases à cocher, utilisez false)
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Son âge :',
                'required' => false,
                'attr' => [
                    'min' => 0, // Définir une valeur minimale pour éviter les valeurs négatives
                    'max' => 30, // Définir une valeur minimale pour éviter les valeurs négatives
                ],
            ])
            ->add('breed', ChoiceType::class, [
                'label' => 'Race du chat :',
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
            ->add('tattoo', ChoiceType::class, [
                'label' => 'A-t-il un tatouage ?',
                'required' => false,
                'choices' => [
                    'Ne sais pas' => 'Ne sais pas',
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
            ])
            ->add('sterelized', ChoiceType::class, [
                'label' => 'Est-il stérilisé/castré ?',
                'required' => false,
                'choices' => [
                    'Ne sais pas' => 'Ne sais pas',
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
            ])
            ->add('designCoat', ChoiceType::class, [
                'label' => 'Comment est son pelage ?',
                'required' => false,
                'choices' => [
                    'Uni' => 'Uni',
                    'Tacheté' => 'Tacheté',
                    'Tigré' => 'Tigré',
                ],
            ])
            ->add('lengthCoat', ChoiceType::class, [
                'label' => 'Taille de son pelage ?',
                'required' => false,
                'choices' => [
                    'Court' => 'Court',
                    'Moyen' => 'Moyen',
                    'Long' => 'Long',
                ],
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'required' => false,
                'choices' => [
                    'Mâle' => 'male',
                    'Femelle' => 'femelle',
                    'Ne sais pas' => 'Ne sais pas',
                ],
            ])
            ->add('chip', TextType::class, [
                'label' => 'Numéro de puce',
                'required' => false,
                'attr' => [
                    'maxlength' => 20,
                ],
            ])

            ->add('picture', FileType::class, [
                'label' => 'Photo :',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            // Ajoutez d'autres types de fichiers autorisés si nécessaire
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier JPG ou PNG valide.',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Cat',
            'csrf_protection' => true, // Activer la protection CSRF
            'csrf_field_name' => '_token', // Nom du champ CSRF
            'csrf_token_id' => 'animal_form', // Identifiant du jeton CSRF
        ]);
    }
}
