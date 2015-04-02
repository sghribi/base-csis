<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FirstSearchType
 */
class FirstSearchType extends SearchType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('booleans');
        $builder->remove('tags');
        $builder->add('tag', 'text', array(
            'attr' => array('id' => 'tag1'),
            'required' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'csis_eambundle_firstsearchtype';
    }
}
