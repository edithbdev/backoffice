<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Your email',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Your email is invalid',
                'error_mapping' => [
                    '.' => 'email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your email is required',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('name', TextType::class, [
                'label' => 'Your name',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Your name is invalid',
                'error_mapping' => [
                    '.' => 'nom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your name is required',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
                'label' => 'Your message',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Your message is invalid',
                'error_mapping' => [
                    '.' => 'message',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Your message is required',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('recaptcha', ReCaptchaType::class, [
                'mapped' => false,
                'invalid_message' => 'Please check the captcha',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Please check the captcha',
                    ]),
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'messages',
        ]);
    }
}
