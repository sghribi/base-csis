<?php
namespace CSIS\EamBundle\Form\DataTransformer;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CSIS\UserBundle\Entity\User;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class UserToEmailTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, Session $session, Router $router)
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

        // Try to find user object by email
        $user = $this->om->getRepository('CSISUserBundle:User')->findOneBy(array('email' => $email ));

        if (null === $user) {
            try {
                $user = new User();
                $user->setEmail($email);
                $user->setEnabled(false);

                // Hack to have unique username
                $suffix = '';
                do {
                    $username = $user->getEmail() . $suffix;
                    $suffix .= '_';
                } while ($this->om->getRepository('CSISUserBundle:User')->findOneBy(array('username' => $username )));
                $user->setUsername($username);

                // Password
                $length = 8;
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
                $password = substr(str_shuffle($chars), 0, $length);
                $user->setPlainPassword($password);

                $this->om->persist($user);
                $this->om->flush();

                $url = $this->router->generate('csis_user_edit', array('id' => $user->getId()) );
                $msg = 'Un compte utilisateur associé au compte mail ' . $email . 'a été créé, mais n\'est pas encore activé. Veuillez compléter les informations pour l\'activer.';
                $link = '<a href="' . $url . '">Cliquez ici</a>';

                $this->session->getFlashBag()->add('valid', ($msg . $link));

            } catch (DBALException $e) {
                throw new TransformationFailedException(sprintf(
                    'La personne avec l\'email "%s" ne peut pas être créée !',
                    $email
                ));
            }
        }
        
        return $user;
    }
}
