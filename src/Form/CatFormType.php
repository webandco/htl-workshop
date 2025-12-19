<?php

namespace App\Form;

use App\Entity\Cat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CatFormType extends AbstractType
{
    public function __construct(private string $alancaptchaSiteKey) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('ownerName', TextType::class)
            ->add('ownerEmail', EmailType::class)
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2048k',
                        extensions: ['png', 'jpg', 'jpeg'],
                        extensionsMessage: 'Please upload a valid Image',
                    )
                ]
            ])
            ->add('alanCaptcha', AlanCaptchaType::class, [
                'mapped' => false,
                'label' => false,
                'site_key' => $this->alancaptchaSiteKey,
                'submit_button_id' => 'cat_form_save'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add Cat'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cat::class,
        ]);
    }
}
