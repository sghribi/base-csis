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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string", length=255, nullable=true)
     * @Assert\MaxLength( 
     *      limit=255
     * )
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(name="floor", type="string", length=255, nullable=true)
     * @Assert\MaxLength( 
     *      limit=255
     * )
     */
    private $floor;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=255, nullable=true)
     * @Assert\MaxLength( 
     *      limit=255
     * )
     */
    private $room;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shared", type="boolean")
     */
    private $shared = false;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     * @Assert\MaxLength( 
     *      limit=255
     * )
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Assert\MaxLength( 
     *      limit=255
     * )
     */
    private $type;

    /**
     * @var CSISEamBundle\Entity\Laboratory
     * 
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Laboratory", inversedBy="equipments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $laboratory;

    /**
     * @var CSISEamBundle\Entity\People
     * 
     * @ORM\ManyToMany(targetEntity="CSIS\EamBundle\Entity\People")
     */
    private $contacts;

    /**
     * @var CSISEamBundle\Entity\Category
     * 
     * @ORM\ManyToMany(targetEntity="CSIS\EamBundle\Entity\Category", inversedBy="equipments")
     */
    private $categories;

    /**
     * @var CSISEamBundle\Entity\Tag
     * 
     * @ORM\ManyToMany(targetEntity="CSIS\EamBundle\Entity\Tag", cascade={"persist"})
     */
    private $tags;

    /**
     * @var CSISUserBundle\Entity\User
     * 
     * @ORM\ManyToMany(targetEntity="CSIS\UserBundle\Entity\User")
     */
    private $owners;

    /**
     * @var datetime $lastEditDate
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
     * Initialize contacts, categories and tags as Doctrine ArrayCollection
     */
    function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setDesignation( $designation )
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setDescription( $description )
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setBuilding( $building )
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setFloor( $floor )
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setRoom( $room )
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
     * @param boolean $shared
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setShared( $shared )
    {
        $this->shared = $shared;

        return $this;
    }

    /**
     * Get shared
     *
     * @return boolean 
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Set brand
     *
     * @param string $brand
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setBrand( $brand )
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
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setType( $type )
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
     * Get Laboratory
     * 
     * @return CSISEamBundle\Entity\Laboratory
     */
    public function getLaboratory()
    {
        return $this->laboratory;
    }

    /**
     * Set Laboratory
     * 
     * @param Laboratory $laboraoty
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setLaboratory( \CSIS\EamBundle\Entity\Laboratory $laboratory )
    {
        $this->laboratory = $laboratory;

        return $this;
    }

    /**
     * Get Contacts
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set Contacts
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $contacts
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setContacts( $contacts )
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get Categories
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set Categories
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $categories
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setCategories( $categories )
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get Tags
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set Tags
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setTags( $tags )
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the owner
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * Set the owner
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $owner
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setOwners( $owners )
    {
        if ( $owners instanceof ArrayCollection ) {
            $this->owners = $owners;
        } else if ( $owners instanceof User ) {
            $this->owners->add($owners);
        } else {
            throw new \InvalidArgumentException('Expected ArrayCollection type or User type, ' . get_class($owners) . ' given.');
        }

        return $this;
    }

    /**
     * Add a contact
     * 
     * @param \CSIS\EamBundle\Entity\CSISEamBundle\Entity\People $contact
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function addContact( $contact )
    {
        $this->contacts->add($contact);

        return $this;
    }

    /**
     * Remove a contact
     * 
     * @param \CSIS\EamBundle\Entity\CSISEamBundle\Entity\People $contact
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function removeContact( \CSIS\EamBundle\Entity\People $contact )
    {
        $this->contacts->removeElement($contact);

        return $this;
    }

    /**
     * Get the date
     * 
     * @return \DateTime()
     */
    public function getLastEditDate()
    {
        return $this->lastEditDate;
    }

    /**
     * Set the date
     * 
     * @param \DateTime $lastModificationDate
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setLastEditDate( \DateTime $lastEditDate )
    {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }
    
    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl ()
    {
      return $this->url;
    }

    /**
     * Set the url
     * @param string $url
     * @return \CSIS\EamBundle\Entity\Equipment
     */
    public function setUrl ( $url )
    {
      $this->url = $url;
      return $this;
    }
    

    /**
     * Add categories
     *
     * @param \CSIS\EamBundle\Entity\Category $categories
     * @return Equipment
     */
    public function addCategory(\CSIS\EamBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \CSIS\EamBundle\Entity\Category $categories
     */
    public function removeCategory(\CSIS\EamBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Add tags
     *
     * @param \CSIS\EamBundle\Entity\Tag $tags
     * @return Equipment
     */
    public function addTag(\CSIS\EamBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \CSIS\EamBundle\Entity\Tag $tags
     */
    public function removeTag(\CSIS\EamBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Add owners
     *
     * @param \CSIS\UserBundle\Entity\User $owners
     * @return Equipment
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
