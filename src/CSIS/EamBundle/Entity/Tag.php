<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\TagRepository")
 */
class Tag {

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

    /**
     * @var datetime $lastEditDate
     *
     * @Gedmo\Timestampable(on="create", on="update")
     * @ORM\Column(type="datetime")
     */
    private $lastEditDate;

    /**
     * Tag's constructor
     * Initialize the status to false (unpublished)
     */
    function __construct() {
        $this->status       = 0;
        $this->lastEditDate = new \DateTime();
    }

    public function __toString() {
        return $this->tag;
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
     * Set tag
     *
     * @param string $tag
     * @return Tag
     */
    public function setTag($tag) {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Tag
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
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
     * @return \CSIS\EamBundle\Entity\Tag
     */
    public function setLastEditDate(\DateTime $lastEditDate) {
        $this->lastEditDate = $lastEditDate;

        return $this;
    }

}
