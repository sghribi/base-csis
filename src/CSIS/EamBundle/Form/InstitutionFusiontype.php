<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstitutionFusionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
            ->add('institutions', 'collection', array(
                'type' => 'csis_institution_selector',
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Classes\InstitutionFusionClass'
        ));
    }

    public function getName() {
        return 'csis_eambundle_instiutionfusiontype';
    }

}
