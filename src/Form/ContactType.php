<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control fs-7',
                    'placeholder' => 'Last name',
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control fs-7',
                    'placeholder' => 'First name',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                // 'help' => 'We will never share your email',
                // 'help_attr' => [
                //     'class' => 'form-text text-muted fs-7',
                // ],
                'attr' => [
                    'class' => 'form-control fs-7',
                    'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                    'placeholder' => 'Email',
                ],
            ])
            ->add('subject', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Subject',
                    'class' => 'form-control fs-7',
                ],
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control fs-7',
                    'placeholder' => 'Message',
                ],
            ])
            ->add('recaptcha', ReCaptchaType::class, [
                'mapped' => false,
                'label' => false,
                'required' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Please check the captcha',
                    ]),
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
