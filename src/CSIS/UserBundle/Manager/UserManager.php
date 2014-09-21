<?php

namespace CSIS\UserBundle\Manager;
use FOS\UserBundle\Model\UserManagerInterface;

class UserManager
{
    /**
     * User manager
     *
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Creates a user and returns it.
     *
     * @param string  $username
     * @param string  $password
     * @param string  $email
     * @param string  $name
     * @param Boolean $active
     * @param Boolean $supera$superAdmin
     *
     * @return UserInterface
     */
    public function create($username, $password, $email, $firstName, $lastName, $active, $superAdmin)
    {
        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPlainPassword($password);
        $user->setEnabled((Boolean)$active);
        if ($superAdmin) {
            $user->setRoles(array('ROLE_ADMIN'));
        } else {
            $user->setRoles(array('ROLE_USER'));
        }
        $this->userManager->updateUser($user);

        return $user;
    }
}
