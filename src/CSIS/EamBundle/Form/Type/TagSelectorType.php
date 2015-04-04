<?php

namespace CSIS\EamBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use CSIS\EamBundle\Form\DataTransformer\TagToTagTransformer;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param EntityManager $em
     * @param SessionInterface $session
     * @param EventDispatcherInterface $dispatcher
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        EntityManager $em,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher,
        SecurityContextInterface $securityContext
    )
    {
        $this->em = $em;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
        $this->securityContext = $securityContext;
    }

    public function buildForm( FormBuilderInterface $builder, array $options)
    {
        $currentUser = $this->securityContext->getToken()->getUser();

        $transformer = new TagToTagTransformer($this->em, $this->session, $options['equipment'], $this->dispatcher, $currentUser);
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
