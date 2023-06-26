<?php

namespace App\Form;

use App\Entity\NoteTags;
use App\Entity\UnitaryNote;
use App\Repository\NoteTagsRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Doctrine\ORM\QueryBuilder;

class UnitaryNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'tags',
                EntityType::class, [
                    'choice_label' => 'name',
                    'class' => NoteTags::class,
                    'query_builder' => function (NoteTagsRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.name', 'ASC');
                    },
                    'multiple' => true,
                    'expanded' => true,                
                ]
            )
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
                    'label' => '一元記録',
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
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UnitaryNote::class,
        ]);
    }
}
