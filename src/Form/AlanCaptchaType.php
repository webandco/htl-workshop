<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlanCaptchaType extends AbstractType
{

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['site_key'] = $options['site_key'];
        $view->vars['submit_button_id'] = $options['submit_button_id'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'site_key' => null,
            'submit_button_id' => null,
            'compound' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'alan_captcha';
    }
}
