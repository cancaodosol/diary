<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file1', FileType::class,
            [
                'label' => 'ファイル1',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                    ])
                ],
            ])
            ->add('file2', FileType::class,
            [
                'label' => 'ファイル2',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                    ])
                ],
            ])
            ->add('file3', FileType::class,
            [
                'label' => 'ファイル3',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                    ])
                ],
            ])
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
