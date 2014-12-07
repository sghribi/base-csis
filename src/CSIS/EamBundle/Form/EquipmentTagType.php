<?php

namespace CSIS\EamBundle\Form;

use CSIS\EamBundle\Entity\EquipmentTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EquipmentTagType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
       /** @var EquipmentTag $equipmentTag */
        $equipmentTag = $event->getData();
        $form = $event->getForm();

        if ($equipmentTag && $equipmentTag->getId()) {
          $form->add('tag', null, array(
              'read_only' => true,
          ));
        } else {
            $form->add('tag');
        }
        $form->add('status', 'hidden');
    });
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
