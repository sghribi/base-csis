<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CSIS\EamBundle\Form\SearchType;
use CSIS\EamBundle\Form\FirstSearchType;

class SuperSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('form1', new FirstSearchType())
			->add('form2', new SearchType())
			->add('form3', new SearchType())
			->add('form4', new SearchType())
			->add('form5', new SearchType())
			->add('form6', new SearchType())
			->add('form7', new SearchType())
			->add('form8', new SearchType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\SuperSearch'
        ));
    }

    public function getName()
    {
        return 'csis_eambundle_supersearchtype';
    }
}
