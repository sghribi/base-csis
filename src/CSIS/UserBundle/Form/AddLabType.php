<?php

namespace CSIS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\EamBundle\Entity\LaboratoryRepository;

class AddLabType extends AbstractType {
    
    protected $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->user;
        
        $builder
            ->add('lab', 'entity', array(
                'label' => 'Laboratoire :',
                'class' => 'CSISEamBundle:Laboratory',
                'query_builder' => function(LaboratoryRepository $er) use ($user) {
                        return $er->getQbReachableLaboratoriesOrderedByAcronym($user);
                },
                'attr' => array(
                    'class'=>'span5',
                ),
            ))
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'csis_userbundle_addlabtype';
    }
}
