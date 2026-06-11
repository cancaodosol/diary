<?php

namespace App\Form;

use App\Entity\NoteTags;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NoteTagsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'タグ名'])
            ->add(
                'displayColor',
                TextType::class,
                [
                    'label' => '表示色',
                    'required' => false,
                ]
            )
            ->add(
                'displayType',
                ChoiceType::class,
                [
                    'label' => '表示タイプ',
                    'choices' => [
                        'タイトル表示' => NoteTags::DISPLAY_TYPE_TITLE,
                        '画像表示' => NoteTags::DISPLAY_TYPE_IMAGE,
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => '説明',
                    'required' => false,
                    'attr' => ['rows' => '4'],
                ]
            )
            ->add(
                'sortOrder',
                IntegerType::class,
                [
                    'label' => '並び順',
                    'required' => false,
                    'empty_data' => '0',
                ]
            )
            ->add(
                'parentTagId',
                IntegerType::class,
                [
                    'label' => '親ID',
                    'required' => false,
                    'empty_data' => null,
                ]
            )
            ->add(
                'text',
                TextareaType::class,
                [
                    'label' => 'テキスト',
                    'required' => false,
                    'attr' => ['rows' => '18'],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                ['label' => '保存']
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
