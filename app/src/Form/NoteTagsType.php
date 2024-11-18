<?php

namespace App\Form;

use App\Entity\NoteTags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NoteTagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parentTagId')
            ->add('name')
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => '説明',
                    'required' => false,
                    'attr' => 
                    [
                        'rows' => '4'
                    ],
                ]
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => 'テキスト',
                    'required' => false,
                    'attr' => 
                    [
                        'rows' => '18'
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => '保存',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteTags::class,
        ]);
    }
}
