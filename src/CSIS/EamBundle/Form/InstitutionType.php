<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstitutionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('acronym', 'text', array(
                    'label' => 'Acronyme',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('name', 'text', array(
                    'label' => 'Nom',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('url', 'text', array(
                    'label' => 'Site web',
                    'attr' => array('class' => 'span7',),
                ))
                ->add('description', 'textarea', array(
                    'label' => 'description',
                    'attr' => array('
                        class' => 'span7',
                        'rows' => 10,
                    ),
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Institution'
        ));
    }

    public function getName() {
        return 'csis_eambundle_institutiontype';
    }

}
