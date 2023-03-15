<?php

namespace App\Form;

use App\Entity\Announce;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ])
            ->add('dateCat', DateType::class,[
                "label"=>"Quand avez vous perdu/retrouvé le chat"
            ])
            ->add('cp', IntegerType::class,[
                "label"=>"Entrez le code postal"
            ])
            ->add('street', TextType::class,[
                "label"=>"Dans quelle rue ?"
            ])
            ->add('city', TextType::class,[
                "label"=>"Dans quelle ville ?"
            ])
            ->add('country', CountryType::class,[
                "label"=>"Dans quelle Pays ?"
            ])
            ->add('description', TextareaType::class,[
                "label"=>"Ajouter une description"
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
