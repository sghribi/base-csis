<?php

namespace CSIS\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends BaseProfileFormType
{
    public function getName()
    {
        return 'csis_user_profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => 'Prénom :'))
            ->add('lastName', 'text', array('label' => 'Nom :'))
            ->add('url', null, array('label' => 'Site Web :'))
            ->add('phoneNumber', null, array('label' => 'Téléphone :'))
        ;
        
        parent::buildUserForm($builder, $options);
        
    }
}
