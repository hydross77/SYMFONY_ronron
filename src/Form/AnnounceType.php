<?php

namespace App\Form;

use App\Entity\Announce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class AnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Perdu' => 'Perdu',
                    'Retrouvé' => 'Retrouvé',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type.',
                    ]),
                ],
            ])
            ->add('dateCat', DateType::class,[
                "label" => "A quelle date ?",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une date.',
                    ]),
                    new Type([
                        'type' => '\DateTimeInterface',
                        'message' => 'La valeur doit être une date valide.',
                    ]),
                ],
            ])
            ->add('city', TextType::class,[
                "label" => "Dans quelle ville ?",
                "attr" => ['class' => 'city'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une ville.',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'La ville doit comporter au moins 2 caractères.',
                        'maxMessage' => 'La ville ne peut pas dépasser 100 caractères.',
                    ]),
                ],
            ])
            ->add('cp', IntegerType::class,[
                "label" => "Code postal :",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un code postal.',
                    ]),
                    new Type([
                        'type' => 'integer',
                        'message' => 'La valeur doit être un nombre entier.',
                    ]),
                ],
            ])
            ->add('street', TextType::class,[
                'required' => false,
                "label" => "Dans quelle rue ?",
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'La rue ne peut pas dépasser 100 caractères.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class,[
                "label" => "Ajouter une description",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description.',
                    ]),
                    new Length([
                        'max' => 400,
                        'maxMessage' => 'La description ne peut pas dépasser 400 caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Announce::class,
        ]);
    }
}

