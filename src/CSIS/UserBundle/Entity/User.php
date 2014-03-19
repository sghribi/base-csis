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
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Laboratory")
     */
    protected $lab;
    
    /**
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Institution")
     */
    protected $institution;

    /**
     * @ORM\OneToOne(targetEntity="CSIS\EamBundle\Entity\People", cascade={"persist","remove"}, inversedBy="userAccount")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $datas;
    
    public function __construct(\CSIS\EamBundle\Entity\People $people = null)
    {
        if ($people == null)
        {
            parent::__construct();
            $this->datas = new \CSIS\EamBundle\Entity\People();
        }
        else
        {
            parent::__construct();
            $this->datas = $people;
        }
    }

    public function __toString() {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }
    
    /**
     * Get the user's id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the email
     * @return string
     */
    public function getEmail() {
        return $this->datas->getEmail();
    }
    

    /**
     * Set the email
     * @param string $email
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setEmail($email) {
        // Ceci permet la synchronisation des mails entre l'entité User et l'entité People. C'est nécessaire.
        $this->email = $email ;
        $this->datas->setEmail($email);
        
        return $this;
    }

    /**
     * Get the user's first name
     * @return string
     */
    public function getFirstName() {
        return $this->datas->getFirstname();
    }

    /**
     * Set the user's first name
     * @param string $firstName
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setFirstName($firstName) {
        $this->datas->setFirstname($firstName);
        
        return $this;
    }

    /**
     * Get the user's last name
     * @return sring
     */
    public function getLastName() {
        return $this->datas->getName();
    }

    /**
     * Set the user's last name
     * @param string $lastName
     * @return \CSIS\UserBundle\Entity\User
     */
    public function setLastName($lastName) {
        $this->datas->setName($lastName);
        
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

    /**
     * Set datas
     *
     * @param \CSIS\EamBundle\Entity\People $datas
     * @return User
     */
    public function setDatas(\CSIS\EamBundle\Entity\People $datas)
    {
        $this->datas = $datas;

        return $this;
    }

    /**
     * Get datas
     *
     * @return \CSIS\EamBundle\Entity\People 
     */
    public function getDatas()
    {
        return $this->datas;
    }
}
