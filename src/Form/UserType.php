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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

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
                // 'empty_data' => '',
                'help' =>
                    'Your email address will be used to log in to the backoffice.',
                'help_attr' => [
                    'class' => 'help-block',
                    'style' =>
                        'color: #7e7b7b; font-size: 0.8em; font-style: italic;',
                ],
                'invalid_message' =>
                    'Your email address is not valid. Please enter a valid email address.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address.',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Firstname',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Lastname',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => 'true',
                // 'empty_data' => '',
                'mapped' => false,
                'first_options' => [
                    // ajouter un astérisc pour le mot de passe sur le label
                    'label' => 'Password',
                    'help' =>
                        'Your password must be at least 6 characters long.',
                    'help_attr' => [
                        'class' => 'help-block',
                        'style' =>
                            'color: #7e7b7b; font-size: 0.8em; font-style: italic,',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password.',
                        ]),
                        new Length([
                            'min' => 6,
                            'max' => 4096,
                            'minMessage' =>
                                'Your password must be at least {{ limit }} characters long.',
                        ]),
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
                    'constraints' => [
                        new NotBlank([
                            'message' =>
                                'The password fields must match.',
                        ]),
                    ],
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
            ->add('captcha', CaptchaType::class, [
                'width' => 160,
                'height' => 50,
                'length' => 4,
                'quality' => 100,
                // bouton pour afficher le captcha
                'as_url' => true,
               
                'reload' => true,
                //translation renew




                'label' => 'Please enter the text displayed in the image',
                'background_color' => [255, 255, 255],
                'invalid_message' => 'The captcha code is invalid.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the text displayed in the imageeee.',
                    ]),
                ],
                'mapped' => false,
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
