<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\People;
use CSIS\UserBundle\Entity\User;
use CSIS\EamBundle\Form\PeopleType;

/**
 * People controller.
 *
 */
class PeopleController extends Controller
{
    /**
     * Lists all People peoples.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');

        $peoples = $em->getRepository('CSISEamBundle:People')->findByOrderNamePaginated($page, $maxPerPage);

        return $this->render('CSISEamBundle:People:index.html.twig', array(
            'peoples' => $peoples,
            'page' => $page,
            'nbPages' => ceil(count($peoples) / $maxPerPage),
        ));
    }

    /**
     * Finds and displays a People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('CSISEamBundle:People')->find($id);

        if (!$people) {
            throw $this->createNotFoundException('Unable to find People entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:People:show.html.twig', array(
            'people'      => $people,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function newAction()
    {
        $people = new People();
        $form   = $this->createForm(new PeopleType(), $people);

        return $this->render('CSISEamBundle:People:new.html.twig', array(
            'people' => $people,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function createAction(Request $request)
    {
        $people  = new People();
        $form = $this->createForm(new PeopleType(), $people);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($people);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Personne ajoutée !');
            return $this->redirect($this->generateUrl('people_show', array('id' => $people->getId())));
        }

        return $this->render('CSISEamBundle:People:new.html.twig', array(
            'people' => $people,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('CSISEamBundle:People')->find($id);

        if (!$people) {
            throw $this->createNotFoundException('Unable to find People entity.');
        }

        $editForm = $this->createForm(new PeopleType(), $people);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:People:edit.html.twig', array(
            'people'      => $people,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('CSISEamBundle:People')->find($id);

        if (!$people) {
            throw $this->createNotFoundException('Unable to find People entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PeopleType(), $people);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($people);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('valid', 'Personne modifiée !');
            return $this->redirect($this->generateUrl('people_edit', array('id' => $id)));
        }

        return $this->render('CSISEamBundle:People:edit.html.twig', array(
            'people'      => $people,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

     /**
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function askDeleteAction(People $people) {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CSISEamBundle:People');
        
        // On vérifie si le contact existe
        $exist = $repo->isPeopleUsed($people->getId());
        if ($exist) {
            $this->get('session')->getFlashBag()->add('error', 'Suppression impossible : le contact <strong>'. $people->getEmail() .'</strong>, est utilisé dans les équipements.');
        } else {
            // Message de confirmation
            if ($people->hasUserAccount()) {
                $message = 'Êtes-vous sûr de bien vouloir supprimer le contact <strong>'.$people->getEmail().'</strong> et le compte utilisateur <strong>' . $people->getUserAccount()->getUsername() . '</strong> associé ?';
            } else {
                $message = 'Êtes-vous sûr de bien vouloir supprimer le contact <strong>'.$people->getEmail().'</strong> ?';
            }
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('people_delete', array('id' => $people->getId())) . '">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('people') . '">Non</a>';
            $this->get('session')->getFlashBag()->add('main_valid',  $message);
        }
        // Redirection vers la page principale
        return $this->redirect($this->generateUrl('people'));
    }
    
    /**
     * Deletes a People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function deleteAction(People $people)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$people) {
            throw $this->createNotFoundException('Unable to find People entity.');
        }

        $em->remove($people);
        $em->flush();
        $this->get('session')->getFlashBag()->add('valid', 'Contact supprimé !');

        return $this->redirect($this->generateUrl('people'));
    }
    
    public function autocompleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $peopleRepo = $em->getRepository('CSISEamBundle:People');
        $input = $request->request->get('input');
        
        if ($request->isXmlHttpRequest() ) {
            $peoples = $peopleRepo->findAutocomplete($input);
            
            $data = array();
            if ( count($peoples) > 0 ) {
                foreach ($peoples as $people) $data[] = $people->getEmail();
            } else {
                $data[] = 'Aucun résulat trouvé. Vous pouvez ajouter un';
                $data[] = 'nouveau contact il sera automatiquement créé.';
            }
            
            return new Response(json_encode($data));
                
        } else {
            throw new AccessDeniedHttpException(
                    'La page à laquelle vous tentez d\'accéder ne fonctionne que par ajax'
            );
        }
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }


    /**
     * Create an User Account for a a People entity.
     * @Secure(roles="ROLE_GEST_PEOPLE")
     */
    public function createUserAccountAction(People $people)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$people) {
            throw $this->createNotFoundException('Unable to find People entity.');
        }

        // Cas où le compte possède déjà un compte utilisateur
        if ($people->hasUserAccount())
        {
            $user = $people->getUserAccount();

            $this->get('session')->getFlashBag()->add('notice', 'Le contact ' . $people . ' possède déjà un compte utilisateur. Cette page permet de modifier ce compte utilisateur.');

            return $this->redirect($this->generateUrl('csis_user_edit', array('username' => $user->getUsername())));
        }

        // Sinon, on va sur le formulaire pour créer un compte utilisateur
        else
        {
            // On crée un nouvel utilisateur
            $user = new User($people);

            // On lie le People et l'User
            $people->setUserAccount($user);

            // On lie l'attribut email
            $user->setEmail($user->getEmail());

            // On génère un login, en s'assurant qu'il n'existe pas
            $username=$user->getFirstName() . '.' . $user->getLastName();
            $num=1;
            while($em->getRepository('CSISUserBundle:User')->findByUsername($username))
            {
                $num++;
                $username = $user->getFirstName() . '.' . $user->getLastName() . $num;
            }
            $user->setUsername($username);

            // On génère un mot de passe aléatoire
            $user->setPassword(uniqid());

            $em->persist($people);
            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Un compte utilisateur a été créé pour le contact ' . $people . ' <' . $people->getEmail() . '> ! Vous pouvez définir ou modifier les données de cette page.');

            return $this->redirect($this->generateUrl('csis_user_edit', array('username' => $user->getUsername())));
        }
    }
}
