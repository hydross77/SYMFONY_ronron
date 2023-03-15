<?php

namespace App\Form;

use App\Entity\Cat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('city', TextType::class, [
                'required'=>false,
                'label' => false,
                'attr' => ['id' => 'city'],
            ])
            ->add('color', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Cat::class,
                'expanded' => false,
                'multiple' => false,
                'choice_label' => 'color',
                'choice_value' => 'id',
            ]);
    }

    //configuration du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}