<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CSIS\EamBundle\Entity\LaboratoryRepository;

class EquipmentType extends AbstractType
{

  protected $user;

  public function __construct ( $user )
  {
    $this->user = $user;
  }

  public function buildForm ( FormBuilderInterface $builder, array $options )
  {
    $user = $this->user;

    if ($options['summary']) {
        $builder
            ->add('designation', null, array(
                'label' => 'Nom de l\'équipement :',
                'attr' => array(),
            ))
            ->add('brand', null, array(
                'label' => 'Marque :',
                'attr' => array(),
            ))
            ->add('type', null, array(
                'label' => 'Type :',
                'attr' => array(),
            ))
            ->add('url', null, array(
                'label' => 'Url :',
                'attr' => array(),
            ))
            ->add('description', null, array(
                'label' => 'Description de l\'équipement :',
                'attr' => array(
                    'rows' => '6',
                ),
            ))
            ->add('shared', 'choice', array(
                'label' => 'Disponible :',
                'choices' => array(
                    '0' => 'Non',
                    '2' => 'À discuter',
                    '1' => 'Oui',
                ),
                'attr' => array(),
                'multiple' => false,
            ))
            ->add('laboratory', 'entity', array(
                'label' => 'Laboratoire :',
                'class' => 'CSISEamBundle:Laboratory',
                'query_builder' => function(LaboratoryRepository $er) use ($user) {
                        return $er->getQbReachableLaboratoriesOrderedByAcronym($user);
                    },
                'attr' => array(),
            ))
            ->add('building', null, array(
                'label' => 'Bâtiment :',
                'attr' => array(),
            ))
            ->add('floor', null, array(
                'label' => 'Étage :',
                'attr' => array(),
            ))
            ->add('room', null, array(
                'label' => 'Salle :',
                'attr' => array(),
            ))
            ->add('owners', 'collection', array(
                'type' => 'csis_user_selector',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ));
    }

      if ($options['tags']) {
          $builder
              ->add('tags', 'collection', array(
                  'type' => 'csis_tags_selector',
                  'allow_add' => true,
                  'allow_delete' => true,
                  'prototype' => true,
                  'by_reference' => false,
                  'options' => array(
                      'equipment' => $options['equipment'],
                  )
          ));
      };
  }

  public function setDefaultOptions ( OptionsResolverInterface $resolver )
  {
    $resolver->setDefaults(array(
        'data_class' => 'CSIS\EamBundle\Entity\Equipment',
        'cascade_validation' => true,
        'summary' => false,
        'tags' => false,
        'equipment' => null,
    ));
  }

  public function getName ()
  {
    return 'csis_eambundle_equipmenttype';
  }
}
