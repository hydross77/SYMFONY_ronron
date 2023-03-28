<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportAnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', ChoiceType::class, [
                'choices' => [
                    'Annonce inappropriée' => 'inappropriate',
                    'Annonce frauduleuse' => 'fraudulent',
                    'Annonce en double' => 'duplicate',
                    'Autre' => 'other',
                ],
                'label' => 'Raison de signalement',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('details', TextareaType::class, [
                'label' => 'Détails (optionnel)',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ]);
    }

}
