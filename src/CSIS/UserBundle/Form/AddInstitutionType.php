<?php

namespace CSIS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use CSIS\EamBundle\Entity\InstitutionRepository;

class AddInstitutionType extends AbstractType {
    
    protected $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->user;
        
        $builder
            ->add('institution', 'entity', array(
                    'class' => 'CSISEamBundle:Institution',
                    'property' => 'name',
                    'label' => 'Etablissement',
                    'query_builder' => function(InstitutionRepository $er) use ($user) {
                        return $er->getQbReachableInstitutions($user);
                    },
                    'attr' => array('class' => 'span7',),
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
        return 'csis_userbundle_addinstitutionype';
    }
}
