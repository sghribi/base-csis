<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EquipmentTag
 *
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
     * @ORM\JoinColumn(name="equipment_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Equipment", inversedBy="equipmentTags")
     */
    private $equipment;

    /**
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Tag", inversedBy="tagEquipments")
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
     * @param \CSIS\EamBundle\Entity\Equipment $equipment
     * @return EquipmentTag
     */
    public function setEquipment(\CSIS\EamBundle\Entity\Equipment $equipment = null)
    {
        $this->equipment = $equipment;

        return $this;
    }

    /**
     * Get equipment
     *
     * @return \CSIS\EamBundle\Entity\Equipment 
     */
    public function getEquipment()
    {
        return $this->equipment;
    }

    /**
     * Set tag
     *
     * @param \CSIS\EamBundle\Entity\Tag $tag
     * @return EquipmentTag
     */
    public function setTag(\CSIS\EamBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \CSIS\EamBundle\Entity\Tag 
     */
    public function getTag()
    {
        return $this->tag;
    }
}
