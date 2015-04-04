<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use CSIS\UserBundle\Entity\User;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Equipment
 *
 * @ORM\Table(name="equipment")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\EquipmentRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Equipment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var ArrayCollection $users
     *
     * @ORM\ManyToMany(targetEntity="CSIS\EamBundle\Entity\Tag", inversedBy="equipments", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="equipment_tag",
     *     joinColumns={@ORM\JoinColumn(name="equipment_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"tag": "ASC"})
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length( 
     *      min=1, 
     *      max=255
     * )
     * @Serializer\Expose
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Serializer\Expose
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
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [#%s]', $this->designation, $this->id);
    }

    /**
     * Constructor of the class
     */
    public function __construct()
    {
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
     * Set laboratory
     *
     * @param Laboratory $laboratory
     *
     * @return Equipment
     */
    public function setLaboratory(Laboratory $laboratory)
    {
        $this->laboratory = $laboratory;

        return $this;
    }

    /**
     * Get laboratory
     *
     * @return Laboratory
     */
    public function getLaboratory()
    {
        return $this->laboratory;
    }

    /**
     * Add owner
     *
     * @param User $owner
     *
     * @return Equipment
     */
    public function addOwner(User $owner = null)
    {
        $this->owners[] = $owner;

        return $this;
    }

    /**
     * Remove owner
     *
     * @param User $owner
     */
    public function removeOwner(User $owner)
    {
        $this->owners->removeElement($owner);
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

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Equipment
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return ArrayCollection
     * @Serializer\VirtualProperty
     */
    public function getAcceptedTags()
    {
        return $this->tags->filter(function(Tag $tag) { return $tag->getStatus() == Tag::ACCEPTED;});
    }

    /**
     * @Assert\Callback
     */
    public function assertTagsAreUnique(ExecutionContextInterface $context)
    {
        $tags = $this->tags->toArray();

        if (count($tags) != count(array_unique($tags))) {
            $context->buildViolation('Des tags sont en doublons.')->addViolation();
        }
    }
}
