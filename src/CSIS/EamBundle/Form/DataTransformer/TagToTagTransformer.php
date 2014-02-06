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

            $this->session->getFlashBag()->add(
                    'main_valid',
                    sprintf('Une demande de validation a été éffectuées au près des administrateurs pour valider le tag "%s".', $tag_name)
            );

            /** ATTENTION
             * CECI est une faille potentielle
             * La récupération de l'id de l'équipement ne devrait pas se faire par l'URL mais par un passage de paramètres
             * A modifier dès qu'une solution est trouvée
             */
            try
            {
                // On récupère l'id de l'équipement en cours
                $id_equipement = (int)preg_split("/[\/]+/", $_SERVER['REQUEST_URI'])[2];
                
                // On récupère l'équipement
                $equipement = $this->om
                ->getRepository('CSISEamBundle:Equipment')
                ->findOneBy(array( 'id' => $id_equipement ))
                 ;

                 // On lie l'équipement et le tag
                 $equipement->addTag($tag);
                 $this->om->persist($equipement);
                 $this->om->flush();
            } catch (\Exception $e)
            {
                $this->session->getFlashBag()->add('main_valid', 'Une erreur s\'est produite dans l\'association du tag et de l\'equipement');
            }
        }
        return $tag;
    }

}
