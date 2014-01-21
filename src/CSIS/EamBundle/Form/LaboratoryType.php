<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\EamBundle\Entity\InstitutionRepository;
use CSIS\EamBundle\Entity\PeopleRepository;

class LaboratoryType extends AbstractType {
    
    protected $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->user;
        
        $builder
                ->add('acronym', 'text', array(
                    'label' => 'Acronyme',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('nameFr', 'text', array(
                    'label' => 'Nom (FR)',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('nameEn', 'text', array(
                    'label' => 'Name (EN)',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('belongToCSIS', 'choice', array(
                    'label' => 'Fait parti du CSIS',
                    'choices' => array(
                        '0' => 'Non',
                        '1' => 'Oui',
                    ),
                    'multiple' => false,
                    'attr' => array('class' => 'span7',),
                ))
                ->add('researchLaboratory', 'choice', array(
                    'label' => 'Est un labo de recherche',
                    'choices' => array(
                        '0' => 'Non',
                        '1' => 'Oui',
                    ),
                    'multiple' => false,
                    'attr' => array('class' => 'span7',),
                ))
                ->add('institution', 'entity', array(
                    'class' => 'CSISEamBundle:Institution',
                    'property' => 'name',
                    'label' => 'Établissement',
                    'query_builder' => function(InstitutionRepository $er) use ($user) {
                        return $er->getQbReachableInstitutions($user);
                    },
                    'attr' => array('class' => 'span7',),
                ))
                ->add('responsible', 'entity', array(
                    'class' => 'CSISEamBundle:People',
                    'label' => 'Responsable',
                    'query_builder' => function(PeopleRepository $er) {
                        return $er->getQbFindAllOrderedByName();
                    },
                    'attr' => array('class' => 'span7',),
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Laboratory'
        ));
    }

    public function getName() {
        return 'csis_eambundle_laboratorytype';
    }

}
