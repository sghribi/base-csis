<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categort
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\CategoryRepository")
 */
class Category
{
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    
    /**
     * @var \CSIS\EamBundle\Entity\Equipment
     * 
     * @ORM\ManyToMany(targetEntity="\CSIS\EamBundle\Entity\Equipment", mappedBy="categories")
     */
    private $equipments;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Categories
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Categories
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Get the equipments
     * 
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getEquipments() {
        return $this->equipments;
    }

    /**
     * Set the equipments
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $equipments
     * @return \CSIS\EamBundle\Entity\Category
     */
    public function setEquipments(\Doctrine\Common\Collections\ArrayCollection $equipments) {
        $this->equipments = $equipments;
        
        return $this;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->equipments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add equipments
     *
     * @param \CSIS\EamBundle\Entity\Equipment $equipments
     * @return Category
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
}
