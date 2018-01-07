<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoLoginForm extends AbstractType {
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('token');
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'csrf_protection' => false,
    ]);
  }
}
