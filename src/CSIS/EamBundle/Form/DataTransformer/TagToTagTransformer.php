<?php

namespace CSIS\EamBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\EamBundle\Entity\Tag;

class TagToTagTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * @param ObjectManager $om
     */
    public function __construct( ObjectManager $om, SessionInterface $session )
    {
        $this->om = $om;
        $this->session = $session;
    }

    /**
     * Transforms an object (tag) to a string (tag).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform( $tag )
    {
        if ( null === $tag ) {
            return "";
        }

        if ( !$tag instanceof Tag ) {
            throw new UnexpectedTypeException($tag, 'CSIS\EamBundle\Entity\Tag');
        }

        return $tag->getTag();
    }

    /**
     * Transforms a string (tag) to an object (tag).
     *
     * @param  string $tag_name
     * @return Tag|null
     * @throws TransformationFailedException if object (tag) is not found.
     */
    public function reverseTransform( $tag_name )
    {
        if ( !$tag_name ) {
            return null;
        }

        $tag = $this->om
                ->getRepository('CSISEamBundle:Tag')
                ->findOneBy(array( 'tag' => $tag_name ))
        ;

        if ( null === $tag ) {
//            throw new TransformationFailedException(sprintf(
//                'Le tag "%s" ne peut pas être trouvé !',
//                $tag_name
//            ));

            $tag = new Tag();
            $tag->setTag($tag_name);
            $tag->setStatus(false);
            $tag->setLastEditDate(new \DateTime());
            
            $this->om->persist($tag);
            $this->om->flush();

            $this->session->getFlashBag()->set(
                    'main_valid',
                    sprintf('Une demande de validation a été éffectuées au près des administrateurs pour valider le tag "%s".', $tag_name)
            );
        }

        return $tag;
    }

}