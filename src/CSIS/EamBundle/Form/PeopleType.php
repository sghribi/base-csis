<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeopleType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('name', 'text', array(
                  'label' => 'Nom',
                  'attr' => array('class'=>'span7',),
                  ))
            ->add('firstname', 'text', array(
                  'label' => 'Prénom',
                  'attr' => array('class'=>'span7',),
                  ))
            ->add('email', 'text', array(
                  'label' => 'e-mail',
                  'attr' => array('class'=>'span7',),
                  ))
            ->add('phoneNumber', 'text', array(
                  'label' => 'N° de téléphone',
                  'attr' => array('class'=>'span7',),
                  ))
            ->add('url', 'text', array(
                  'label' => 'site web',
                  'attr' => array('class'=>'span7',),
                  ))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'CSIS\EamBundle\Entity\People'
    ));
  }

  public function getName() {
    return 'csis_eambundle_peopletype';
  }

}
