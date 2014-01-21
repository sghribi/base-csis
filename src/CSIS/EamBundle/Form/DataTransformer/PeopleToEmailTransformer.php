<?php
namespace CSIS\EamBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\EamBundle\Entity\People;

class PeopleToEmailTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    private $session;
    private $router;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $session, $router)
    {
        $this->om = $om;
        $this->session = $session;
        $this->router  = $router;
    }

    /**
     * Transforms an object (people) to a string (email).
     *
     * @param  People|null $people
     * @return string
     */
    public function transform($people)
    {
        if (null === $people) {
            return "";
        }
        
        if (!$people instanceof People) {
            throw new UnexpectedTypeException($people, 'CSIS\EamBundle\Entity\People');
        }

        return $people->getEmail();
    }

    /**
     * Transforms a string (email) to an object (people).
     *
     * @param  string $email
     * @return People|null
     * @throws TransformationFailedException if object (people) is not found.
     */
    public function reverseTransform($email)
    {
        if (!$email) {
            return null;
        }
        
        $people = $this->om
            ->getRepository('CSISEamBundle:People')
            ->findOneBy(array('email' => $email ))
        ;

        if (null === $people) {
//          throw new TransformationFailedException(sprintf(
//              'La personne avec l\'email "%s" ne peut pas être trouvée !',
//              $email
//          ));
            $people = new People();
            $people->setEmail($email);
            $this->om->persist($people);
            $this->om->flush();
            
            $url = $this->router->generate('people_edit', array('id' => $people->getId()) );
            $msg = 'Le contact que vous avez associé n\'existe pas encore. Veuillez compléter ses informations.';
            $link = '<a href="' . $url . '">Cliquez ici</a>';
            
            $this->session->getFlashBag()->set('main_valid', ($msg . $link) );
        }
        
        return $people;
    }
}