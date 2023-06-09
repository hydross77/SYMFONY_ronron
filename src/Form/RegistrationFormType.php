<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['autocomplete' => 'off'], // Ajout d'un attribut pour désactiver l'autocomplétion du navigateur
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo',
                'attr' => ['autocomplete' => 'off'], // Ajout d'un attribut pour désactiver l'autocomplétion du navigateur
            ])
            ->add('phone', TelType::class, [ // Utilisation du type 'TelType' pour le champ de téléphone
                'label' => 'Votre numéro de téléphone - optionnel',
                'required' => false, // Rendre le champ optionnel
                'attr' => ['autocomplete' => 'off'], // Ajout d'un attribut pour désactiver l'autocomplétion du navigateur
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'En cochant cette case, j\'accepte les conditions d\'utilisation générale du site.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Acceptez les CGU',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'mapped' => true,
                'first_options' => [
                    'label' => 'Mot de passe : ',
                    'attr' => ['class' => 'input-full'],
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'match' => true,
                            'message' => 'Le mot de passe doit contenir : minimum 8 caractères, un nombre, une minuscule, une majuscule et un caractère spécial',
                        ]),
                    ],
                ],

                'second_options' => [
                    'label' => 'Répétez le mot de passe : ',
                    'attr' => ['class' => 'input-full'],
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'match' => true,
                            'message' => 'Le mot de passe doit contenir : minimum 8 caractères, un nombre, une minuscule, une majuscule et un caractère spécial',
                        ]),
                    ],
                ],
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
