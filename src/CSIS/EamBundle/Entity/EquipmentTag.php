<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EquipmentTag
 *
 * @UniqueEntity(fields={"equipment", "tag"})
 * @ORM\Table(name="equipment_tag")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\EquipmentTagRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EquipmentTag
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
     * @ORM\JoinColumn(name="equipment_id", referencedColumnName="id", nullable=false)
     *
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Equipment", inversedBy="equipmentTags", cascade={"persist"})
     */
    private $equipment;

    /**
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", nullable=false)
     *
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Tag", cascade={"persist"})
     */
    private $tag;


    /**
    * @var integer
    *
    * @ORM\Column(name="status", type="integer")
    */
    private $status;

    const ACCEPTED = 0;
    const REFUSED = 1;

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
     * @return EquipmentTag
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
     * Set equipment
     *
     * @param Equipment $equipment
     *
     * @return EquipmentTag
     */
    public function setEquipment(Equipment $equipment)
    {
        $this->equipment = $equipment;

        return $this;
    }

    /**
     * Get equipment
     *
     * @return Equipment
     */
    public function getEquipment()
    {
        return $this->equipment;
    }

    /**
     * Set tag
     *
     * @param Tag $tag
     * @return EquipmentTag
     */
    public function setTag(Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}
