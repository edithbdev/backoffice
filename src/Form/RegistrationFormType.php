<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('phone', NumberType::class, [
                'label' => 'Phone Number',
                'required' => false,
            ])
            ->add('captcha', CaptchaType::class, [
                'width' => 140,
                'height' => 40,
                'length' => 5,
                'quality' => 100,
                // bouton pour afficher le captcha
                'as_url' => true,
                'reload' => true,
                'label' => 'Please enter the text displayed in the image',
                'background_color' => [255, 255, 255],
                'invalid_message' => 'The captcha code is invalid.',
                'constraints' => [
                    new NotBlank([
                        'message' =>
                            'Please enter the text displayed in the image.',
                    ]),
                ],
                'mapped' => false,
            ])
            // ->add('agreeTerms', CheckboxType::class, [
            //     'mapped' => false,
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'You should agree to our terms.',
            //         ]),
            //     ],
            // ])
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
