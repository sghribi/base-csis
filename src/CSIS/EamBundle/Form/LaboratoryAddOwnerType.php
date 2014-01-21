<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\UserBundle\Entity\UserRepository;

class LaboratoryAddOwnerType extends AbstractType {
    
    var $user;
    
    public function __construct($user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->user;
        
        $builder
                ->add('owners', 'entity', array(
                    'class' => 'CSISUserBundle:User',
                    'label' => ' ',
                    'multiple' => false,
                    'expanded' => false,
                    'attr' => array('class' => 'span4'),
                    'query_builder' => function(UserRepository $er) use ($user) {
                            return $er->getQbFindSuperiorWhithLabRole($user);
                    }
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Laboratory'
        ));
    }

    public function getName() {
        return 'csis_eambundle_laboratoryAddOwnertype';
    }

}
