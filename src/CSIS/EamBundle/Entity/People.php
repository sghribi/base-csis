<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * People
 *
 * @ORM\Table(name="people")
 * @ORM\Entity(repositoryClass="CSIS\EamBundle\Entity\PeopleRepository")
 */
class People
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=false)
     * 
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     * 
     * @Assert\Url()
     */
    private $url;


    /**
     * @ORM\OneToOne(targetEntity="CSIS\UserBundle\Entity\user", mappedBy="datas")
     */
    private $userAccount; 


    public function __toString()
    {
        if ( strlen($this->getName()) == 0 && strlen($this->getFirstname()) == 0 ) {
            return $this->getEmail();
        }
        $str = $this->getName() . ' ' . $this->getFirstname();
        return $str;
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
     * Set name
     *
     * @param string $name
     * @return People
     */
    public function setName( $name )
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
     * Set firstname
     *
     * @param string $firstname
     * @return People
     */
    public function setFirstname( $firstname )
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return People
     */
    public function setEmail( $email )
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return People
     */
    public function setPhoneNumber( $phoneNumber )
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return People
     */
    public function setUrl( $url )
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
     * Set userAccount
     *
     * @param \CSIS\UserBundle\Entity\user $userAccount
     * @return People
     */
    public function setUserAccount(\CSIS\UserBundle\Entity\user $userAccount = null)
    {
        $this->userAccount = $userAccount;

        return $this;
    }

    /**
     * Get userAccount
     *
     * @return \CSIS\UserBundle\Entity\user 
     */
    public function getUserAccount()
    {
        return $this->userAccount;
    }

    /**
     * Has user account ?
     * @return bool
     */
    public function hasUserAccount()
    {
        if ($this->userAccount == null)
        {
            return false;
        }
        return true;
    }
}
