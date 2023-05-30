<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DiaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'date',
            DateType::class,
            [
                'label' => '日付',
                'required' => true,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ]
        )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => '行動記録',
                    'required' => true,
                    'attr' => 
                    [
                        'rows' => '18'
                    ],
                ]
            )
            ->add(
                'startedWritingAt',
                HiddenType::class,
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => '保存',
                ]
                );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
