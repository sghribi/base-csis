<?php

namespace CSIS\UserBundle\Entity;

use CSIS\EamBundle\Entity\Institution;
use CSIS\EamBundle\Entity\Laboratory;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="user")
 * @UniqueEntity("email")
 * @ORM\Entity(repositoryClass="CSIS\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Entrez un prénom s'il vous plait")
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Entrez un nom s'il vous plait")
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     */
    protected $phoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    protected $url;

    /**
     * Laboratoire dans lequel travaille l'utilisateur.
     * ATTENTION : cela ne donne aucun droit à l'utilisateur
     * Les droits sont gérés via Laboratory::$owners
     *
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Laboratory")
     */
    protected $lab;

    /**
     * Institution dans laquelle travaille l'utilisateur
     * ATTENTION : cela ne donne aucun droit à l'utilisateur
     * Les droits sont gérés via Institution::$owners
     *
     * @ORM\ManyToOne(targetEntity="CSIS\EamBundle\Entity\Institution")
     */
    protected $institution;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLastName() . ' ' . $this->getFirstName() . ' <' . $this->getEmail() . '>';
    }

    /**
     * @return array
     */
    public function getFormatedRoles()
    {
        $roles = $this->getRoles();
        array_walk($roles, function(&$role) {
            $role = ucwords(strtolower(str_replace('_', ' ', substr($role, 5))));
        });

        return $roles;
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
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
     * @return User
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
     * Set lab
     *
     * @param Laboratory $lab
     * @return User
     */
    public function setLab(Laboratory $lab = null)
    {
        $this->lab = $lab;

        return $this;
    }

    /**
     * Get lab
     *
     * @return Laboratory
     */
    public function getLab()
    {
        return $this->lab;
    }

    /**
     * Set institution
     *
     * @param Institution $institution
     *
     * @return User
     */
    public function setInstitution(Institution $institution = null)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return Institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }
}
