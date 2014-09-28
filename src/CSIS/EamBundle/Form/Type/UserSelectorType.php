<?php

namespace CSIS\EamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\EamBundle\Form\DataTransformer\UserToEmailTransformer;

class UserSelectorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;
    private $session;
    private $router;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $session, $router) {
        $this->om = $om;
        $this->session = $session;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $transformer = new UserToEmailTransformer($this->om, $this->session, $this->router);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'invalid_message' => 'L\'utilisteur sélectionné n\'existe pas',
            'label' => 'Adresse email de l\'utilisateur :',
            'attr' => array(
                'class' => 'span5',
            )
        ));
    }

    public function getParent() {
        return 'email';
    }

    public function getName() {
        return 'csis_user_selector';
    }
}
