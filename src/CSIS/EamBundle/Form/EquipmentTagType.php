<?php

namespace CSIS\EamBundle\Form;

use CSIS\EamBundle\Entity\EquipmentTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EquipmentTagType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('tag')
        ->add('status', 'choice', array(
            'choices' => array(
                EquipmentTag::ACCEPTED => 'Accepté',
                EquipmentTag::REFUSED => 'Refusé',
            )
        ))
    ;
  }

  public function setDefaultOptions ( OptionsResolverInterface $resolver )
  {
    $resolver->setDefaults(array(
        'data_class' => 'CSIS\EamBundle\Entity\EquipmentTag',
        'cascade_validation' => true,
    ));
  }

  public function getName ()
  {
    return 'csis_eambundle_equipmenttagtype';
  }
}
