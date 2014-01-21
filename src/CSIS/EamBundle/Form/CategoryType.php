<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                  'label' => 'Nom',
                  'attr' => array('class'=>'span7',),
                  ))
            ->add('description', 'textarea', array(
                  'label' => 'Description',
                  'attr' => array('class'=>'span7',),
                  ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Category'
        ));
    }

    public function getName()
    {
        return 'csis_eambundle_categorytype';
    }
}
