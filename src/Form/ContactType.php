<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class, [
                'label' => 'Votre email',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Votre email est invalide',
                'error_mapping' => [
                    '.' => 'email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre email',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('nom',TextType::class, [
                'label' => 'Votre nom',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Votre nom est invalide',
                'error_mapping' => [
                    '.' => 'nom',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
                'label' => 'Votre message',
                'required' => true,
                'empty_data' => '',
                'error_bubbling' => true,
                'invalid_message' => 'Votre message est invalide',
                'error_mapping' => [
                    '.' => 'message',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre message',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
            ->add('captcha', CaptchaType::class, [
                'width' => 160,
                'height' => 50,
                'length' => 4,
                'quality' => 100,
                'background_color' => [255, 255, 255],
                'invalid_message' => 'Le captcha est invalide',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le captcha',
                    ]),
                ],
                'translation_domain' => 'messages',
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'messages',
        ]);
    }
}
