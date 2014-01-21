<?php

namespace CSIS\EamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Persistence\ObjectManager;

use CSIS\EamBundle\Entity\LaboratoryRepository;

class LaboratorySelectorType extends AbstractType {

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
            'label' => 'Laboratoire à fusionner :',
            'class' => 'CSISEamBundle:Laboratory',
            'query_builder' => function(LaboratoryRepository $er) use ($user) {
                return $er->getQbByOwnersOrderByAcronym($user);
            },
            'empty_value' => 'Choisissez un laboratoire',
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
        return 'csis_laboratory_selector';
    }

}
