<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LaboratoryFusionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
            ->add('laboratories', 'collection', array(
                'type' => 'csis_laboratory_selector',
                'allow_add' => true,
                'by_reference' => false,
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Classes\LaboratoryFusionClass'
        ));
    }

    public function getName() {
        return 'csis_eambundle_laboratoryfusiontype';
    }

}
