<?php

namespace App\Form;

use App\Entity\Project;
use App\Form\TechnoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\FileType as FileTypeConstraint;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du projet',
                'attr' => [
                    'placeholder' => 'Nom du projet',
                ],
            ])
            ->add('description', null, [
                'label' => 'Description du projet',
                'attr' => [
                    'placeholder' => 'Description du projet',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier de type jpeg ou png',
                    ]),
                ],
            ])
            ->add('link', null, [
                'label' => 'Lien du projet',
                'attr' => [
                    'placeholder' => 'Lien du projet',
                ],
            ])
            ->add('techno', EntityType::class, [
                'class' => 'App\Entity\Techno',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('created_At', null, [
                'label' => 'Date de création',
                'attr' => [
                    'placeholder' => 'Date de création',
                ],
            ])
             ->add('isPublished', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,


        ]);
    }
}
