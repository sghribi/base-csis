<?php

namespace CSIS\EamBundle\Classes;

use CSIS\EamBundle\Entity\Laboratory;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of LaboratoryFusionClass
 *
 * @author Cedric
 */
class LaboratoryFusionClass {

    private $laboratories;

    public function __construct() {
        $this->laboratories = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLaboratories() {
        return $this->laboratories;
    }

    /**
     * @param CSIS\EamBundle\Entity\Laboratory $laboratories
     * @return \CSIS\EamBundle\Classes\LaboratoryFusionClass
     */
    public function setLaboratories(ArrayCollection $laboratories) {
        $this->laboratories = $laboratories;
        return $this;
    }

    /**
     * @param CSIS\EamBundle\Entity\Laboratory $laboratory
     * @return \CSIS\EamBundle\Classes\LaboratoryFusionClass
     */
    public function addLaboratories(Laboratory $laboratory) {
        $this->laboratories->add($laboratory);
        return $this;
    }

    /**
     * 
     * @param CSIS\EamBundle\Entity\Laboratory $laboratory
     * @return boolean
     */
    public function removeLaboratories(Laboratory $laboratory) {
        return $this->laboratories->removeElement($laboratory);
    }

    /**
     * Allow fusion between selected laboratories and the parameter one
     * @param \CSIS\EamBundle\Entity\Laboratory $laboratory
     */
    public function fusionWith(Laboratory $laboratory) {
        foreach ($this->laboratories as $lab) {
            $laboratory->addEquipments($lab->getEquipments(), false);
            $lab->getEquipments()->clear();
            
            $laboratory->addOwners($lab->getOwners(), false);
            $lab->getOwners()->clear();
        }
    }

}
