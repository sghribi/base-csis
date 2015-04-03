<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tag
 *
 * @UniqueEntity(fields={"tag"})
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\TagRepository")
 */
class Tag
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
     * @ORM\Column(name="tag", type="string", length=255, unique=true, nullable=false)
     * 
     * @Assert\NotBlank()
     * @Assert\Length( 
     *      min=1, 
     *      max=255
     * ) 
     */
    private $tag;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    const PENDING = 0;
    const ACCEPTED = 1;
    const REFUSED = 2;

    /**
     * @var \DateTime $lastEditDate
     *
     * @Gedmo\Timestampable(on="create", on="update")
     * @ORM\Column(type="datetime")
     */
    private $lastEditDate;

    /**
     * @var ArrayCollection $equipments
     *
     * @ORM\ManyToMany(targetEntity="CSIS\EamBundle\Entity\Equipment", mappedBy="tags", cascade={"all"})
     */
    private $equipments;

    /**
     * Tag's constructor
     * Initialize the status to false (unpublished)
     */
    public function __construct()
    {
        $this->status       = self::PENDING;
        $this->lastEditDate = new \DateTime();
        $this->equipments   = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->tag;
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
     * Set status
     *
     * @param integer $status
     * @return Tag
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lastEditDate
     *
     * @param \DateTime $lastEditDate
     * @return Tag
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
     * Set tag
     *
     * @param string $tag
     * @return Tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    public function getTagCanonical()
    {

        $tag = mb_strtolower($this->tag, 'UTF-8');
        $tag = str_replace(array(  'à', 'â', 'ä', 'á', 'ã', 'å',
                                    'î', 'ï', 'ì', 'í', 
                                    'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
                                    'ù', 'û', 'ü', 'ú', 
                                    'é', 'è', 'ê', 'ë', 
                                    'ç', 'ÿ', 'ñ', 
                            ),
                            array(  'a', 'a', 'a', 'a', 'a', 'a', 
                                    'i', 'i', 'i', 'i', 
                                    'o', 'o', 'o', 'o', 'o', 'o', 
                                    'u', 'u', 'u', 'u', 
                                    'e', 'e', 'e', 'e', 
                                    'c', 'y', 'n', 
                            ),
                            $tag
                );
        return $tag;
    }

    /**
     * Add equipment
     *
     * @param Equipment $equipment
     *
     * @return Tag
     */
    public function addEquipment(Equipment $equipment)
    {
        $this->equipments[] = $equipment;

        return $this;
    }

    /**
     * Remove equipments
     *
     * @param Equipment $equipment
     */
    public function removeEquipment(Equipment $equipment)
    {
        $this->equipments->removeElement($equipment);
    }

    /**
     * Get equipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipments()
    {
        return $this->equipments;
    }
}
