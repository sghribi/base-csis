<?php

namespace CSIS\EamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\EamBundle\Form\DataTransformer\TagToTagTransformer;

class TagSelectorType extends AbstractType
{

    /**
     * @var ObjectManager
     */
    private $om;
    
    /**
     * @var Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @param ObjectManager $om
     */
    public function __construct( ObjectManager $om, SessionInterface $session )
    {
        $this->om = $om;
        $this->session = $session;
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $transformer = new TagToTagTransformer($this->om, $this->session);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Le tag sélectionné n\'existe pas.',
            'label' => 'Nom du tag :',
            'attr' => array(
                'class' => 'span2',
            )
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'csis_tag_selector';
    }

}
