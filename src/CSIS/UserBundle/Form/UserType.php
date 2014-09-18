<?php

namespace CSIS\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseProfileFormType;
use FOS\UserBundle\Util\UserManipulator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;

class RegistrationFormType extends BaseProfileFormType {

  /**
   * @var Symfony\Component\Security\Core\Role\RoleHierarchy 
   */
  private $securityHierarchy;

  /**
   * @var FOS\UserBundle\Util\UserManipulator
   */
  private $userManipulator;
  
  /**
   * @var Symfony\Component\Security\Core\SecurityContext
   */
  private $securityContext;
  
  public function __construct($class, SecurityContext $security_context, RoleHierarchy $security_hierarchy, UserManipulator $user_manipulator) {
    parent::__construct($class);
    $this->securityHierarchy = $security_hierarchy;
    $this->userManipulator   = $user_manipulator;
    $this->securityContext   = $security_context;
  }

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('firstName', 'text', array('label' => 'Prénom :'))
            ->add('lastName', 'text', array('label' => 'Nom de famille :'))
    ;

    parent::buildForm($builder, $options);
    
    $user = $this->securityContext->getToken()->getUser();
    if (!$user) { throw new \LogicException('The CSISRegistrationFormType cannot be used without an authenticated user!'); }
    $factory = $builder->getFormFactory();
    $securityHierarchy = $this->securityHierarchy;

    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function(FormEvent $event) use($user, $factory, $securityHierarchy){
        $ownRoles = $user->getRoles();
        // Transforme les rôles (string) en rôles (objets)
        $roles_class = array();
        foreach( $ownRoles as $role) { $roles_class[] = new Role($role); }
        // Récupère les rôles en portées
        $roles_class = $securityHierarchy->getReachableRoles( $roles_class );
        // Retransforme les rôles (objet) en rôle (srting)
        $roles = array();
        foreach( $roles_class as $role_class ) { 
          $roles[$role_class->getRole()] = ucwords(strtolower(str_replace('_', ' ', substr($role_class->getRole(), 5)))); 
        }
        // Supprime tous les doublons et on ne garde que les rôles frontaux
        $authorized_role = array('Admin', 'Gest Estab', 'Gest Lab', 'Gest Equip', 'Gest Tags', 'Gest Category');
        $roles = array_intersect(array_unique($roles), $authorized_role);
        
        $formOptions = array(
            'choices'  => $roles,
            'required' => true,
            'multiple' => true,
            'label'    => 'Rôles :',
        );

        // create the field, this is similar to the $builder->add() method
        // field name, field type, data, options
        $event->getForm()->add($factory->createNamed('roles', 'choice', null, $formOptions));
      }
    );
  }

  public function getName() {
    return 'csis_user_registration';
  }

}
