<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CSIS\EamBundle\Form\SearchType;

class FirstSearchType extends SearchType // ici, on h�rite de SearchTYpe
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		// On fait appel � la m�thode buildForm du parent, qui va ajouter tous les champs � $builder
		parent::buildForm($builder, $options);
        $builder->remove('booleans');
		$builder->remove('tags');
		$builder->add('tag', 'text', array( 
				'attr' => array('id' => 'tag1'),
				'required' => true
				));
    }

    public function getName()
    {
        return 'csis_eambundle_firstsearchtype';
    }
}
