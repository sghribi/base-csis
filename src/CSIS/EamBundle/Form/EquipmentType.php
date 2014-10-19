<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    $builder
            ->add('designation', 'text', array(
                'label' => 'Nom de l\'équipement :',
                'attr' => array(),
            ))
            ->add('brand', 'text', array(
                'label' => 'Marque :',
                'attr' => array(),
            ))
            ->add('type', 'text', array(
                'label' => 'Type :',
                'attr' => array(),
            ))
            ->add('url', 'url', array(
                'label' => 'Url :',
                'attr' => array(),
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description de l\'équipement :',
                'attr' => array(
                    'rows' => '6',
                ),
            ))
            ->add('shared', 'choice', array(
                'label' => 'Disponible :',
                'choices' => array(
                    '0' => 'Non',
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
            ->add('building', 'text', array(
                'label' => 'Bâtiment :',
                'attr' => array(),
            ))
            ->add('floor', 'text', array(
                'label' => 'Étage :',
                'attr' => array(),
            ))
            ->add('room', 'text', array(
                'label' => 'Salle :',
                'attr' => array(),
            )) //@TODO: replace with user
            ->add('owners', 'collection', array(
                'type' => 'csis_user_selector',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            // ->add('tags', 'collection', array(
            //     'type' => 'csis_tag_selector',
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            // ))
            ->add('categories', 'entity', array(
                'class' => 'CSISEamBundle:Category',
                'property' => 'name',
                'multiple' => true,
                'required' => false,
            ))
    ;
  }

  public function setDefaultOptions ( OptionsResolverInterface $resolver )
  {
    $resolver->setDefaults(array(
        'data_class' => 'CSIS\EamBundle\Entity\Equipment'
    ));
  }

  public function getName ()
  {
    return 'csis_eambundle_equipmenttype';
  }
}
