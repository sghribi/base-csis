<?php

namespace CSIS\EamBundle\Event;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Entity\Tag;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TagEvent
 */
class TagEvent extends Event
{
    /**
     * @var Tag
     */
    private $tag;

    /**
     * @var Equipment
     */
    private $equipment;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param Tag $tag
     */
    public function __construct(Tag $tag, Equipment $equipment, UserInterface $user)
    {
        $this->tag = $tag;
        $this->equipment = $equipment;
        $this->user = $user;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Equipment
     */
    public function getEquipment()
    {
        return $this->equipment;
    }
}
