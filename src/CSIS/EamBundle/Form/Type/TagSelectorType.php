<?php

namespace CSIS\EamBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use CSIS\EamBundle\Form\DataTransformer\TagToTagTransformer;

class TagSelectorType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param EntityManager     $em
     * @param SessionInterface  $session
     */
    public function __construct(EntityManager $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $transformer = new TagToTagTransformer($this->em, $this->session, $options['equipment']);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults(array(
            'label' => 'Nom du tag :',
            'equipment' => null,
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'csis_tags_selector';
    }
}
