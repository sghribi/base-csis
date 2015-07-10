<?php

namespace CSIS\EamBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserEvent
 */
class UserEvent extends Event
{

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
