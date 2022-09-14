<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => 'Image (JPG file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG file',
                    ]),
                ],
            ])
            ->add('link', UrlType::class, [
                'label' => 'Link to the project',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.example.com',
                    'pattern' => '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$',
                ],
                'constraints' => [
                    new Url([
                        'message' => 'The url "{{ value }}" is not a valid url',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'The url "{{ value }}" is too short. It should have {{ limit }} characters or more.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('isPublished')
            ->add('Languages')
            // ->add('submit', SubmitType::class, [
            //     'label' => 'Save',
            //     'attr' => [
            //         'class' => 'btn btn-primary',
            //     ],
            // ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
