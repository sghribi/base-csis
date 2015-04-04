<?php

namespace CSIS\UserBundle\Form\Type;

use CSIS\EamBundle\Form\ChoiceList\RoleChoiceList;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseProfileFormType;
use FOS\UserBundle\Util\UserManipulator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

class RegistrationFormType extends BaseProfileFormType {

    /**
     * @var RoleHierarchy
     */
    private $securityHierarchy;

    /**
     * @var UserManipulator
     */
    private $userManipulator;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var boolean
     */
    private $psswd_req;

    private $class;

    public function __construct($class, SecurityContext $security_context, RoleHierarchy $security_hierarchy, UserManipulator $user_manipulator, $paswd_req = true) {
        parent::__construct($class);
        $this->class = $class;
        $this->securityHierarchy = $security_hierarchy;
        $this->userManipulator   = $user_manipulator;
        $this->securityContext   = $security_context;
        $this->psswd_req         = $paswd_req;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName', 'text', array('label' => 'Prénom :'))
            ->add('lastName', 'text', array('label' => 'Nom de famille :'))
            ->add('url', null, array('label' => 'Site Web :'))
            ->add('phoneNumber', null, array('label' => 'Téléphone :'))
        ;

        if (!$options['password']) {
            $builder->remove('plainPassword');
        } else {
            $builder
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                    'required' => $this->psswd_req,
                ));
        }

        $builder->add('roles', 'choice', array(
            'choice_list' => new RoleChoiceList(),
            'by_reference' => false,
            'label' => 'Rôles',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
            'password' => true,
        ));
    }

    public function getName() {
        return 'csis_user_registration';
    }
}
