<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use CSIS\UserBundle\Entity\User;

/**
 * Institution
 *
 * @ORM\Table(name="institution")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\InstitutionRepository")
 */
class Institution {

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
     * @ORM\Column(name="acronym", type="string", length=255)
     */
    private $acronym;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var User
     * 
     * @ORM\ManyToMany(targetEntity="CSIS\UserBundle\Entity\User")
     */
    private $owners;

    /**
     * @var Laboratory
     *
     * Liste des utilisateurs qui ont un droit sur cet institution
     * 
     * @ORM\OneToMany(targetEntity="Laboratory", mappedBy="institution")
     */
    private $laboratories;

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
        return $this->acronym . ' - ' . $this->name;
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
     * @return Institution
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
     * Set name
     *
     * @param string $name
     * @return Institution
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the owner
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOwners() {
        return $this->owners;
    }

    /**
     * Set the owner
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $owners
     * @return \CSIS\EamBundle\Entity\Equipment
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
     * Get the laboratories
     * 
     * @return ArrayCollection
     */
    public function getLaboratories() {
        return $this->laboratories;
    }

    /**
     * Set the laboratories
     * 
     * @param type $laboratories
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function setLaboratories($laboratories) {
        $this->laboratories = $laboratories;

        return $this;
    }
    
    /**
     * Add one or multiple owners, if owners implements Collection interface
     * you have the possibility of add or override the owners; default: true
     * 
     * @param Collection|Laboratory $laboratory
     * @param boolean $override
     * @return \CSIS\EamBundle\Entity\Laboratory
     * @throws \InvalidArgumentException
     */
    public function addLaboratories($laboratories, $override = true) {
        if ( $laboratories instanceof Laboratory ) {
            if ( !$this->laboratories->contains($laboratories) ) {
                $laboratories->setInstitution($this);
                $this->laboratories->add($laboratories);
            }
        } else if ( $laboratories instanceof Collection && $override) {
            $this->laboratories = $laboratories;
        } else if ( $laboratories instanceof Collection && !$override ) {
            foreach ($laboratories as $laboratorie) $this->addLaboratories($laboratorie);
        } else {
            throw new \InvalidArgumentException('Expected Collection insterface or User type, ' . get_class($laboratories) . ' given.');
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
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function setLastEditDate(\DateTime $lastEditDate) {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }
    
    /**
     * Get the institution's description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set the institution's description
     * @param string $description
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }

    /**
     * Get the institution's url
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set the institution's url
     * @param string $url
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function setUrl($url) {
        $this->url = $url;
        
        return $this;
    }


    /**
     * Add owners
     *
     * @param \CSIS\UserBundle\Entity\User $owners
     * @return Institution
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

    /**
     * Add laboratories
     *
     * @param \CSIS\EamBundle\Entity\Laboratory $laboratories
     * @return Institution
     */
    public function addLaboratory(\CSIS\EamBundle\Entity\Laboratory $laboratories)
    {
        $this->laboratories[] = $laboratories;

        return $this;
    }

    /**
     * Remove laboratories
     *
     * @param \CSIS\EamBundle\Entity\Laboratory $laboratories
     */
    public function removeLaboratory(\CSIS\EamBundle\Entity\Laboratory $laboratories)
    {
        $this->laboratories->removeElement($laboratories);
    }
}
