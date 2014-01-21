<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\UserBundle\Entity\UserRepository;

class InstitutionAddOwnerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('owners', 'entity', array(
                    'class' => 'CSISUserBundle:User',
                    'label' => ' ',
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => array('class' => 'span4'),
                    'query_builder' => function(UserRepository $er) {
                            return $er->getQbfindAllWithEstabRole();
                    }
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Institution'
        ));
    }

    public function getName() {
        return 'csis_eambundle_institutionAddOwnertype';
    }

}
