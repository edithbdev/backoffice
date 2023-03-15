<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\BackendLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BackendLanguageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Name',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ])
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'label' => 'Add a project',
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'style' => 'display: block; font-weight: bold; text-transform: uppercase;',
                ],
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.deleted = 0')
                        ->orderBy('p.name', 'ASC');
                },
                'required' => false,
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Slug',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BackendLanguage::class,
        ]);
    }
}
