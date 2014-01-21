<?php

namespace CSIS\EamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\EamBundle\Form\EventListener\AddTagStatusFieldSubsricber;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new AddTagStatusFieldSubsricber($builder->getFormFactory());
        
        $builder
            ->add('tag', 'text')
            ->addEventSubscriber($subscriber)
          ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Tag'
        ));
    }

    public function getName()
    {
        return 'csis_eambundle_tagtype';
    }
}
