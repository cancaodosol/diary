<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
                'title',
                TextType::class,
                [
                    'label' => 'タイトル',
                ],
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => '行動記録',
                    'required' => false,
                    'attr' => 
                    [
                        'rows' => '18'
                    ],
                ]
            )
            ->add(
                'startedAt',
                TextType::class,
                [
                    'label' => '開始時刻',
                    'required' => true,
                ],
            )
            ->add(
                'finishedAt',
                TextType::class,
                [
                    'label' => '終了時刻',
                    'required' => false,
                ],
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => '保存',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
