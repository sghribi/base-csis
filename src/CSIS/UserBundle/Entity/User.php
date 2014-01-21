<?php

namespace CSIS\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CSIS\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The name is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="255", message="The name is too long.", groups={"Registration", "Profile"})
     */
    protected $firstName;
    
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\MinLength(limit="3", message="The name is too short.", groups={"Registration", "Profile"})
     * @Assert\MaxLength(limit="255", message="The name is too long.", groups={"Registration", "Profile"})
     */
    protected $lastName;
    
    /**
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Laboratory")
     */
    protected $lab;
    
    /**
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Institution")
     */
    protected $institution;
    
    public function __toString() {
        return $this->lastName . ' ' . $this->firstName;
    }
    
    /**
     * Get the user's id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get the user's first name
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set the user's first name
     * @param string $firstName
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        
        return $this;
    }

    /**
     * Get the user's last name
     * @return sring
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set the user's last name
     * @param string $lastName
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
        
        return $this;
    }
    
    /**
     * Get the user's laboratory
     * @return \CSIS\EamBundle\Entity\Laboratory
     */
    public function getLab() {
        return $this->lab;
    }
    
    /**
     * Set the user's laboratory
     * @param \CSIS\EamBundle\Entity\Laboratory $lab
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setLab($lab = null) {
        $this->lab = $lab;
        
        return $this;
    }
    
    /**
     * Get the user's institution
     * @return \CSIS\EamBundle\Entity\Institution
     */
    public function getInstitution() {
        return $this->institution;
    }
    
    /**
     * Set the user's institution
     * @param \CSIS\EamBundle\Entity\Institution $institution
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setInstitution($institution = null) {
        $this->institution = $institution;
        
        return $this;
    }
    
    public function getFormatedRoles() {
        $roles = $this->getRoles();
        array_walk($roles, function(&$role) {
            $role = ucwords(strtolower(str_replace('_', ' ', substr($role, 5))));
        });
        
        return $roles;
    }
}
