<?php

namespace CSIS\EamBundle\Form\ChoiceList;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\LazyChoiceList;

class RoleChoiceList extends LazyChoiceList
{
    /**
     * {@inheritdoc}
     */
    protected function loadChoiceList()
    {
        $choices = array(
            new ArrayCollection(array('ROLE_USER')),
            new ArrayCollection(array('ROLE_USER', 'ROLE_GEST_TAGS')),
            new ArrayCollection(array('ROLE_GEST_EQUIP')),
            new ArrayCollection(array('ROLE_GEST_EQUIP', 'ROLE_GEST_TAGS')),
            new ArrayCollection(array('ROLE_GEST_LAB')),
            new ArrayCollection(array('ROLE_GEST_LAB', 'ROLE_GEST_TAGS')),
            new ArrayCollection(array('ROLE_GEST_ESTAB')),
            new ArrayCollection(array('ROLE_GEST_ESTAB', 'ROLE_GEST_TAGS')),
            new ArrayCollection(array('ROLE_ADMIN')),
        );
        $labels = array(
            'Utilisateur',
            'Utilisateur + gestionnaire des tags',
            'Gestionnaire d\'un laboratoire',
            'Gestionnaire d\'un laboratoire + gestionnaire des tags',
            'Gestionnaire d\'un établissement',
            'Gestionnaire d\'un établissement + gestionnaire des tags',
            'Gestionnaire de tous les établissements',
            'Gestionnaire de tous les établissements + gestionnaire des tags',
            'Super administrateur',
        );

        return new ChoiceList($choices, $labels);
    }
}
