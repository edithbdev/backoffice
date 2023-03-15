<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'given-name',
                ],
                'label' => 'First name',
                'label_attr' => [
                    'class' => 'form-label  mt-4',
                ],
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'family-name',
                ],
                'label' => 'Last name',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ])
            ->add('email', EmailType::class, [
                // Input elements should have autocomplete attributes (suggested: "username")
                'attr' => [
                    'class' => 'form-control',
                    'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                    'autocomplete' => 'off',
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label mt-4',
                ],
            ])
            ->add('roles', ChoiceType::class, [
                // 'choices' => User::class,
                'choices' => [
                    'Administrator' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'choice_attr' => [
                    'selected' => 'ROLE_USER',
                ],
                'label' => 'Roles',
                'label_attr' => [
                    'class' => 'mr-4',
                    'style' => 'display: block;',
                ],
                'multiple' => true,
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
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => [
                    'attr' => [
                        'class' => 'form-control mb-4',
                        'autocomplete' => 'new-password',
                    ],
                    'label' => 'Password',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password',
                    ],
                    'label' => 'Repeat Password',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                ],
            ])
            ->add('isVerified', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-check-input mt-2',
                    // 'style' => 'margin-left: 0.5rem;',
                ],
                'label_attr' => [
                    'class' => 'mt-2',
                ],
                'label' => 'mail verified',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
