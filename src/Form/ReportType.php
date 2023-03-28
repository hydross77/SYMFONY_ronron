<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Expliquez la raison du signalement',
            ])
            ->add('reason', ChoiceType::class, [
                'label' => 'Raison du signalement',
                'choices' => [
                    'Spam' => 'spam',
                    'Harcèlement' => 'harcelement',
                    'Contenu inapproprié' => 'inapproprie',
                ],
                'expanded' => false,
                'multiple' => false,
            ])
        ;
    }

}
