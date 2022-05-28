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

class UserType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'mapped' => true,
                'required' => true,
                'empty_data' => '',
                'help' =>
                    'Votre nom d\'utilisateur doit contenir entre 3 et 20 caractères',
                'help_attr' => [
                    'class' => 'help-block',
                    'style' =>
                        'color: #7e7b7b; font-size: 0.8em; font-style: italic;',
                ],
                'invalid_message' =>
                    'Votre nom d\'utilisateur doit contenir entre 3 et 20 caractères',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom d\'utilisateur',
                    ]),
                    new Length([
                        'min' => 3,
                        'max' => 20,
                        'minMessage' =>
                            'Votre nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                        'maxMessage' =>
                            'Votre nom d\'utilisateur ne peut pas contenir plus de {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'required' => true,
                'empty_data' => '',
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'help' =>
                        'Le mot de passe doit être de 6 caractères minimum',
                    'help_attr' => [
                        'class' => 'help-block',
                        'style' =>
                            'color: #7e7b7b; font-size: 0.8em; font-style: italic,',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer votre mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'max' => 4096,
                            'minMessage' =>
                                'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                    'help' => 'Le mot de passe doit être identique',
                    'help_attr' => [
                        'class' => 'help-block',
                        'style' =>
                            'color: #7e7b7b; font-size: 0.8em; font-style: italic,',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' =>
                                'Les deux mots de passe saisies sont différents',
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
                'label' => 'Veuillez saisir les 4 caractères',
                'background_color' => [255, 255, 255],
                'invalid_message' => 'Le captcha est invalide',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le captcha',
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
