<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'first_options'  => [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'autocomplete' => 'new-password',
                    'onfocus' => 'this.type="text"',
                    'onblur' => 'this.type="password"',
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
                    'onfocus' => 'this.type="text"',
                    'onblur' => 'this.type="password"',
                ],
                'label' => 'Repeat Password',
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
