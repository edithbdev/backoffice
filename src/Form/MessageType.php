<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Last name',
                'required' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Last name',
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'First name',
                'required' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'First name',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                    'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                    'placeholder' => 'Email',
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => 'Subject',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Message',
                ],
            ])
            ->add('isRead', CheckboxType::class, [
                'label' => 'Read',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('isAnswered', CheckboxType::class, [
                'label' => 'Answered',
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comment',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
