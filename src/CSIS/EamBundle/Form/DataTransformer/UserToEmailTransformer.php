<?php
namespace CSIS\EamBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\UserBundle\Entity\User;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class UserToEmailTransformer implements DataTransformerInterface
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
     * @param  User|null $user
     * @return string
     */
    public function transform($user)
    {
        if (null === $user) {
            return "";
        }
        
        if (!$user instanceof User) {
            throw new UnexpectedTypeException($user, 'CSIS\UserBundle\Entity\User');
        }

        return $user->getEmail();
    }

    /**
     * Transforms a string (email) to an object (user).
     *
     * @param  string $email
     * @return User|null
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($email)
    {
        if (!$email) {
            return null;
        }
        
        $user = $this->om
            ->getRepository('CSISUserBundle:User')
            ->findOneBy(array('email' => $email ))
        ;

        if (null === $user) {
          throw new TransformationFailedException(sprintf(
              'La personne avec l\'email "%s" ne peut pas être trouvée !',
              $email
          ));


            // @TODO: handle ...
//            $people = new People();
//            $people->setEmail($email);
//            $this->om->persist($people);
//            $this->om->flush();
//
//            $url = $this->router->generate('people_edit', array('id' => $people->getId()) );
//            $msg = 'Le contact que vous avez associé n\'existe pas encore. Veuillez compléter ses informations.';
//            $link = '<a href="' . $url . '">Cliquez ici</a>';
//
//            $this->session->getFlashBag()->set('main_valid', ($msg . $link) );
        }
        
        return $user;
    }
}
