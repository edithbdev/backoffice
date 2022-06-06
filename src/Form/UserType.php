<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'mapped' => true,
                'required' => true,
                'invalid_message' =>
                    'Your email address is not valid. Please enter a valid email address.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address.',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('phone', NumberType::class, [
                'label' => 'Phone Number',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'mapped' => false,
                // 'attr' => ['autocomplete' => 'new-password'],
                 'options'  => ['attr' => ['class' => 'password-field']],
                'first_options' => [
                    'label' => 'Password',
                    'help' =>
                        'Your password must be at least 6 characters long.',
                    'help_attr' => [
                        'class' => 'help-block',
                        'style' =>
                            'color: #7e7b7b; font-size: 0.8em; font-style: italic,',
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'help' => 'Please repeat your password.',
                    'help_attr' => [
                        'class' => 'help-block',
                        'style' =>
                            'color: #7e7b7b; font-size: 0.8em; font-style: italic,',
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' =>
                            'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::class,
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('created_At', DateType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Date de création',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create Account',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
