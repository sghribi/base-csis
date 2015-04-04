<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use CSIS\UserBundle\Entity\User;

/**
 * Laboratory
 *
 * @UniqueEntity("acronym")
 * @ORM\Table(name="laboratory")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\LaboratoryRepository")
 */
class Laboratory {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="acronym", type="string", length=255)
     */
    private $acronym;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="nameFr", type="string", length=255)
     */
    private $nameFr;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="nameEn", type="string", length=255)
     */
    private $nameEn;

    /**
     * @var boolean
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="researchLaboratory", type="boolean")
     */
    private $researchLaboratory = true;

    /**
     * @var \CSIS\EamBundle\Entity\Institution
     *
     * @Assert\NotBlank(message="Vous devez attacher ce laboratoire à une institution.")
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Institution", inversedBy="laboratories")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id", nullable=false)
     */
    private $institution;

    /**
     * @var \CSIS\EamBundle\Entity\Equipment
     * 
     * @ORM\OneToMany(targetEntity="\CSIS\EamBundle\Entity\Equipment", mappedBy="laboratory")
     * @ORM\OrderBy({"designation": "ASC"})
     */
    private $equipments;

    /**
     * @var User[]
     *
     * Liste des utilisateurs qui ont un droit sur ce laboratoire
     *
     * @ORM\ManyToMany(targetEntity="CSIS\UserBundle\Entity\User")
     */
    private $owners;

    /**
     * @var \DateTime $lastEditDate
     *
     * @Gedmo\Timestampable(on="create", on="update")
     * @ORM\Column(type="datetime")
     */
    private $lastEditDate;

    public function __construct() {
        $this->owners       = new ArrayCollection();
        $this->lastEditDate = new \DateTime();
    }
    
    public function __toString() {
        $dotted = (strlen($this->nameFr) > 60) ? '...' : '';
        return $this->acronym . ' - ' . substr($this->nameFr, 0, 60) . $dotted;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set acronym
     *
     * @param string $acronym
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setAcronym($acronym) {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * Get acronym
     *
     * @return string 
     */
    public function getAcronym() {
        return $this->acronym;
    }

    /**
     * Set nameFr
     *
     * @param string $nameFr
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setNameFr($nameFr) {
        $this->nameFr = $nameFr;

        return $this;
    }

    /**
     * Get nameFr
     *
     * @return string 
     */
    public function getNameFr() {
        return $this->nameFr;
    }

    /**
     * Set nameEn
     *
     * @param string $nameEn
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setNameEn($nameEn) {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get nameEn
     *
     * @return string 
     */
    public function getNameEn() {
        return $this->nameEn;
    }

    /**
     * Set researchLaboratory
     *
     * @param boolean $researchLaboratory
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setResearchLaboratory($researchLaboratory) {
        $this->researchLaboratory = $researchLaboratory;

        return $this;
    }

    /**
     * Get researchLaboratory
     *
     * @return boolean 
     */
    public function getResearchLaboratory() {
        return $this->researchLaboratory;
    }

    /**
     * Get Institution
     * 
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function getInstitution() {
        return $this->institution;
    }

    /**
     * Set Institution
     * 
     * @param \CSIS\EamBundle\Entity\Institution $institution
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setInstitution(\CSIS\EamBundle\Entity\Institution $institution) {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get the owners
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOwners() {
        return $this->owners;
    }

    /**
     * Set the owners
     * 
     * @param ArrayCollection|User $owners
     * @return \CSIS\EamBundle\Entity\Equipment
     * @throws \InvalidArgumentException
     */
    public function setOwners($owners) {
        $this->addOwners($owners);

        return $this;
    }
    
    /**
     * Add one or multiple owners, if owners implements Collection interface
     * you have the possibility of add or override the owners; default: true
     * 
     * @param Collection|User $owners
     * @param boolean $override
     * @return \CSIS\EamBundle\Entity\Equipment
     * @throws \InvalidArgumentException
     */
    public function addOwners($owners, $override = true) {
        if ( $owners instanceof User) {
            if ( !$this->owners->contains($owners) ) {
                $this->owners->add($owners);
            }
        } else if ( $owners instanceof Collection && $override) {
            $this->owners = $owners;
        } else if ( $owners instanceof Collection && !$override ) {
            foreach ($owners as $owner) $this->addOwners($owner);
        } else {
            throw new \InvalidArgumentException('Expected Collection insterface or User type, ' . get_class($owners) . ' given.');
        }
        
        return $this;
    }

    /**
     * Get the equipments
     * 
     * @return ArrayCollection
     */
    public function getEquipments() {
        return $this->equipments;
    }

    /**
     * Set the equipments
     * 
     * @param type $equipments
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setEquipments(ArrayCollection $equipments) {
        $this->equiements = $equipments;

        return $this;
    }
    
    /**
     * Add one or multiple equipment, if equipments implements Collection interface
     * you have the possibility of add or override the equipments; default: true
     * 
     * @param ArrayCollection|Equipment $equipments
     * @param boolean $override
     * @throws \InvalidArgumentException
     */
    public function addEquipments($equipments, $override = true) {
        if ( $equipments instanceof Equipment) {
            if ( !$this->equipments->contains($equipments) ) {
                $equipments->setLaboratory($this);
                $this->equipments->add($equipments);
            }
        } else if ( $equipments instanceof Collection && $override) {
            $this->equipments = $equipments;
        } else if ( $equipments instanceof Collection && !$override ) {
            foreach ($equipments as $equipemt) $this->addEquipments($equipemt);
        } else {
            throw new \InvalidArgumentException('Expected ArrayCollection type or Equipment type, ' . get_class($equipments) . ' given.');
        }
        
        return $this;
    }
    
    /**
     * Get the date
     * 
     * @return \DateTime()
     */
    public function getLastEditDate() {
        return $this->lastEditDate;
    }

    /**
     * Set the date
     * 
     * @param \DateTime $lastModificationDate
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function setLastEditDate(\DateTime $lastEditDate) {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }


    /**
     * Add equipments
     *
     * @param \CSIS\EamBundle\Entity\Equipment $equipments
     * @return Laboratory
     */
    public function addEquipment(\CSIS\EamBundle\Entity\Equipment $equipments)
    {
        $this->equipments[] = $equipments;

        return $this;
    }

    /**
     * Remove equipments
     *
     * @param \CSIS\EamBundle\Entity\Equipment $equipments
     */
    public function removeEquipment(\CSIS\EamBundle\Entity\Equipment $equipments)
    {
        $this->equipments->removeElement($equipments);
    }

    /**
     * Add owners
     *
     * @param \CSIS\UserBundle\Entity\User $owners
     * @return Laboratory
     */
    public function addOwner(\CSIS\UserBundle\Entity\User $owners)
    {
        $this->owners[] = $owners;

        return $this;
    }

    /**
     * Remove owners
     *
     * @param \CSIS\UserBundle\Entity\User $owners
     */
    public function removeOwner(\CSIS\UserBundle\Entity\User $owners)
    {
        $this->owners->removeElement($owners);
    }
}
