<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SearchType
 */
class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('booleans', 'choice', array(
                'choices' => array(
                    'AND' => 'ET',
                    'OR' => 'OU',
                    'AND NOT' => 'NON',
                ),
                'multiple' => false,
                'expanded' => true,
                'label' => ' ',
                'attr' => array('id' => 'booleans1'),
                'required' => false
            ))
            ->add('open', 'text', array(
                'label' => '(',
                'attr' => array('class' => 'parenthese', 'maxlength' => '8'),
                'required' => false
            ))
            ->add('tag', 'text', array(
                    'attr' => array('id' => 'tag1'),
                    'required' => false
            ))
            ->add('close', 'text', array(
                    'label' => ')',
                    'attr' => array('class' => 'parenthese', 'maxlength' => '8'),
                    'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Search'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'csis_eambundle_searchtype';
    }
}
