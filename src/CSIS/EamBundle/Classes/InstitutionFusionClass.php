<?php

namespace CSIS\EamBundle\Classes;

use CSIS\EamBundle\Entity\Institution;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of InstitutionFusionClass
 *
 */
class InstitutionFusionClass {
    
    private $institutions;
    
    public function __construct() {
        $this->institutions = new ArrayCollection();
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getInstitutions() {
        return $this->institutions;
    }

    /**
     * @param CSIS\EamBundle\Entity\Institution $institutions
     * @return \CSIS\EamBundle\Classes\InstitutionFusionClass
     */
    public function setInstitutions(ArrayCollection $institutions) {
        $this->institutions = $institutions;
        return $this;
    }

    /**
     * @param CSIS\EamBundle\Entity\Institution $institution
     * @return \CSIS\EamBundle\Classes\InstitutionFusionClass
     */
    public function addInstitutions(Institution $institution) {
        $this->institutions->add($institution);
        return $this;
    }
    
    /**
     * 
     * @param CSIS\EamBundle\Entity\Institution $institution
     * @return boolean
     */
    public function removeInstitutions(Institution $institution) {
        return $this->institutions->removeElement($institution);
    }
    
    /**
     * Allow fusion between selected institutions and the parameter one
     * @param \CSIS\EamBundle\Entity\Institution $institution
     */
    public function fusionWith(Institution $institution) {
        foreach ($this->institutions as $inst) {
            $institution->addLaboratories($inst->getLaboratories(), false);
            $inst->getLaboratories()->clear();
            
            $institution->addOwners($inst->getOwners(), false);
            $inst->getOwners()->clear();
        }
    }
    
}
