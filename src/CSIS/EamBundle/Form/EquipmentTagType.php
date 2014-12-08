<?php

namespace CSIS\EamBundle\Form;

use CSIS\EamBundle\Entity\EquipmentTag;
use CSIS\EamBundle\Entity\Tag;
use Doctrine\ORM\EntityRepository;
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
        } elseif ($equipmentTag && !$equipmentTag->getId()) {
            $form->add('tag', 'entity', array(
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->where('t.status = :tagAccepted')
                            ->orWhere('t.status = :tagPending')
                            ->setParameter('tagAccepted', Tag::ACCEPTED)
                            ->setParameter('tagPending', Tag::PENDING)
                            ->orderBy('t.tag');
                    },
                'class' => 'CSISEamBundle:Tag',
            ));
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
