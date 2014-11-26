<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use CSIS\UserBundle\Entity\User;

/**
 * Equipment
 *
 * @ORM\Table(name="equipment")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\EquipmentRepository")
 */
class Equipment
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
      * @ORM\OneToMany(targetEntity="CSIS\EamBundle\Entity\EquipmentTag", cascade={"persist"}, mappedBy="equipment")
      */
    private $equipmentTags;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length( 
     *      min=1, 
     *      max=255
     * ) 
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max=255
     * )
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(name="floor", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max=255
     * )
     */
    private $floor;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max=255
     * )
     */
    private $room;

    /**
     * @var boolean
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="shared", type="integer")
     */
    private $shared;

    const NOT_SHARED = 0;
    const SHARED = 1;
    const DISCUTABLE = 2;


    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max=255
     * )
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max=255
     * )
     */
    private $type;

    /**
     * @var Laboratory
     * 
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Laboratory", inversedBy="equipments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $laboratory;

    /**
     * @var User
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
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url()
     */
    private $url;

    /**
     * Constructor of the class
     */
    function __construct()
    {
	    $this->equipmentTags = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->lastEditDate = new \DateTime();
    }

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
     * Set designation
     *
     * @param string $designation
     * @return Equipment
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string 
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Equipment
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
     * Set building
     *
     * @param string $building
     * @return Equipment
     */
    public function setBuilding($building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return string 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set floor
     *
     * @param string $floor
     * @return Equipment
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return string 
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set room
     *
     * @param string $room
     * @return Equipment
     */
    public function setRoom($room)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return string 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set shared
     *
     * @param integer $shared
     * @return Equipment
     */
    public function setShared($shared)
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return integer
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set brand
     *
     * @param string $brand
     * @return Equipment
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Equipment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set lastEditDate
     *
     * @param \DateTime $lastEditDate
     * @return Equipment
     */
    public function setLastEditDate($lastEditDate)
    {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }

    /**
     * Get lastEditDate
     *
     * @return \DateTime 
     */
    public function getLastEditDate()
    {
        return $this->lastEditDate;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Equipment
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Get Tags
     *
     */
    public function getTags()
    {
        $equipmentTags = $this->equipmentTags;

        $tags = new ArrayCollection();

        foreach ($equipmentTags as $equipmentTag)
        {
            $tags[] = $equipmentTag->getTag();
        }

        return $tags;
    }

    /**
     * Set Tags
     *
     * @author Samy
     */
    public function setTags($tags)
    {
        $equipmentTags = $this->getEquipmentTags();

        foreach ($equipmentTags as $equipmentTag)
        {
            $this->removeEquipmentTag($equipmentTag);
        }

        foreach ($tags as $tag)
        {
            $equipmentTag = new EquipmentTag();

            $equipmentTag->setEquipment($this);
            $equipmentTag->setTag($tag);

            $this->addEquipmentTag($equipmentTag);
        }
    }

    /**
     * Add equipmentTags
     *
     * @param \CSIS\EamBundle\Entity\EquipmentTag $equipmentTags
     * @return Equipment
     */
    public function addEquipmentTag(\CSIS\EamBundle\Entity\EquipmentTag $equipmentTags)
    {
        $this->equipmentTags[] = $equipmentTags;
		//$tagEquipments->setTag($this);
        return $this;
    }

    /**
     * Remove equipmentTags
     *
     * @param \CSIS\EamBundle\Entity\EquipmentTag $equipmentTags
     */
    public function removeEquipmentTag(\CSIS\EamBundle\Entity\EquipmentTag $equipmentTags)
    {
        $this->equipmentTags->removeElement($equipmentTags);
		//$tagEquipment->setTag(null);
    }

    /**
     * Get equipmentTags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEquipmentTags()
    {
        return $this->equipmentTags;
    }

    /**
     * Set laboratory
     *
     * @param \CSIS\EamBundle\Entity\Laboratory $laboratory
     * @return Equipment
     */
    public function setLaboratory(\CSIS\EamBundle\Entity\Laboratory $laboratory)
    {
        $this->laboratory = $laboratory;

        return $this;
    }

    /**
     * Get laboratory
     *
     * @return \CSIS\EamBundle\Entity\Laboratory 
     */
    public function getLaboratory()
    {
        return $this->laboratory;
    }

    /**
     * Add owner
     *
     * @param User $owner
     * @return Equipment
     */
    public function addOwner(\CSIS\UserBundle\Entity\User $owner = null)
    {
        $this->owners[] = $owner;

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
     * Get owners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwners()
    {
        return $this->owners;
    }
}
