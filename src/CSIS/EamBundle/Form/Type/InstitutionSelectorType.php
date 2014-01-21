<?php

namespace CSIS\EamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Persistence\ObjectManager;

use CSIS\EamBundle\Entity\InstitutionRepository;

class InstitutionSelectorType extends AbstractType {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;
    
    /**
     * @var \CSIS\UserBundle\Entity\User
     */
    private $user;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, SecurityContext $securityContext) {
        $this->om = $om;
        $this->user = $securityContext->getToken()->getUser();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $user = $this->user;
        
        $resolver->setDefaults(array(
            'label' => 'Etablissements à fusionner :',
            'class' => 'CSISEamBundle:Institution',
            'query_builder' => function(InstitutionRepository $er) use ($user) {
                return $er->getQbByOwnersOrderByAcronym($user);
            },
            'empty_value' => 'Choisissez un établissement',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'attr' => array(
                'class' => 'span7',
            )
        ));
    }

    public function getParent() {
        return 'entity';
    }

    public function getName() {
        return 'csis_institution_selector';
    }

}
