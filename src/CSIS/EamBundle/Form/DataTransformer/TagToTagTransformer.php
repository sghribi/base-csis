<?php

namespace CSIS\EamBundle\Form\DataTransformer;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Event\TagEvent;
use CSIS\EamBundle\Events;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use CSIS\EamBundle\Entity\Tag;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TagToTagTransformer
 */
class TagToTagTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SessionInterface|Session
     */
    private $session;

    /**
     * @var Equipment
     */
    private $equipment;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var UserInterface
     */
    private $user;


    /**
     * @param EntityManager             $em
     * @param SessionInterface          $session
     * @param Equipment                 $equipment
     * @param EventDispatcherInterface  $dispatcher
     * @param UserInterface             $user
     */
    public function __construct(
        EntityManager $em,
        SessionInterface $session,
        Equipment $equipment,
        EventDispatcherInterface $dispatcher,
        UserInterface $user
    )
    {
        $this->em = $em;
        $this->session = $session;
        $this->equipment = $equipment;
        $this->dispatcher = $dispatcher;
        $this->user = $user;
    }

    /**
     * Transforms an object (tag) to a string (tag).
     *
     * @param  Tag|null $tag
     * @return string
     */
    public function transform( $tag )
    {
        if ( null === $tag ) {
            return "";
        }

        if (!$tag instanceof Tag) {
            throw new UnexpectedTypeException($tag, 'CSIS\EamBundle\Entity\Tag');
        }

        return $tag->getTag();
    }

    /**
     * Transforms a string (tag) to an object (tag).
     *
     * @param  string $tag_name
     *
     * @return Tag|null
     * @throws TransformationFailedException if object (tag) is not found.
     */
    public function reverseTransform($tag_name)
    {
        if (!$tag_name) {
            return null;
        }

        $tag = $this->em
                ->getRepository('CSISEamBundle:Tag')
                ->findOneBy(array('tag' => $tag_name))
        ;

        if (!$tag) {
            /** @var Equipment $equipment */
            $equipment = $this->em->getPartialReference('CSIS\EamBundle\Entity\Equipment', $this->equipment->getId());

            $tag = new Tag();
            $tag->setTag($tag_name);
            $tag->setStatus(Tag::PENDING);
            $tag->setLastEditDate(new \DateTime());
            $equipment->addTag($tag);
            $this->em->persist($tag);
            $this->em->flush();

            $this->dispatcher->dispatch(Events::TAG_CREATED, new TagEvent($tag, $equipment, $this->user));

            $this->session->getFlashBag()->add(
                    'main_valid',
                    sprintf('Une demande de validation a été éffectuées au près des administrateurs pour valider le tag "%s".', $tag_name)
            );
        }

        return $tag;
    }
}
