<?php

namespace App\Form;

use App\Entity\Tool;
use App\Entity\Project;
use App\Entity\Enum\Status;
use App\Entity\BackendLanguage;
use App\Entity\FrontendLanguage;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Name',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Slug',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ])
            ->add('projectLink', UrlType::class, [
                'label' => 'Link to the project',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.example.com',
                    'pattern' =>
                    '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$',//phpcs:ignore
                ],
                'required' => false,
            ])
            ->add('githubLink', UrlType::class, [
                'label' => 'Link to the project on Github',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.github.com',
                    'pattern' =>
                    '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$',//phpcs:ignore
                ],
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'class' => Status::class,
                'label' => 'Choose the project status',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'choice_label' => fn ($choice) => match ($choice) {
                    Status::Draft => 'draft',
                    Status::Published => 'online',
                    Status::Archived => 'archive',
                    default => 'draft',
                },
                'choice_value' => 'value',
                'placeholder' => false,
                'required' => true,
            ])
            ->add('year', TextType::class, [
                'label' => 'Year',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('lastUpdate', DateType::class, [
                'label' => 'Last update',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'required' => false,
            ])
            ->add('description', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Thumbnail',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'mapped' => false,
                'required' => false,
                'delete_label' => false,
                'allow_delete' => false,
                'image_uri' => false,
                'download_uri' => false,
                'attr' => [
                    'class' => 'file',
                    'type' => 'file',
                    'id' => 'project_imageFile_file',
                    'data-preview-file-type' => "text",
                ],
                'csrf_protection' => true,
                'csrf_field_name' => 'delete',
                'csrf_token_id' => 'project.id',
            ])
            ->add('images', FileType::class, [
                'label' => 'Images carousel',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new All([
                        new Image([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image',
                        ]),
                    ]),
                ],
                'help' => 'You can add until 10 images',
                'help_attr' => [
                    'class' => 'form-text text-muted',
                ],
                'attr' => [
                    'type' => "file",
                    'class' => "file",
                    'data-preview-file-type' => "text",
                    'id' => 'project_images',
                ],
                'csrf_protection' => true,
                'csrf_field_name' => 'delete',
                'csrf_token_id' => 'image.id',

            ])
            ->add('backendLanguages', EntityType::class, [
                'class' => BackendLanguage::class,
                'multiple' => true,
                'label' => 'Backend languages',
                'expanded' => true,
                'label_attr' => [
                    'style' => 'display: block; font-weight: bold; text-transform: uppercase;',
                ],
                'choice_label' => 'name',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.deleted = 0')
                        ->orderBy('l.name', 'ASC');
                },
            ])
            ->add('frontendLanguages', EntityType::class, [
                'class' => FrontendLanguage::class,
                'multiple' => true,
                'label' => 'Frontend languages',
                'expanded' => true,
                'label_attr' => [
                    'style' => 'display: block; font-weight: bold; text-transform: uppercase;',
                ],
                'choice_label' => 'name',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.deleted = 0')
                        ->orderBy('l.name', 'ASC');
                },
            ])
            ->add('tools', EntityType::class, [
                'class' => Tool::class,
                'multiple' => true,
                'choice_label' => function ($tool) {
                    return $tool->getName() . ($tool->getDescription() ? ' : ' . $tool->getDescription() : '');
                },
                'label' => 'Tools',
                'label_attr' => [
                    'style' => 'display: block; font-weight: bold; text-transform: uppercase;',
                ],
                'expanded' => true,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                        ->where('t.deleted = 0')
                        ->orderBy('t.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
