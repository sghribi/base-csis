<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\People;
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
        $form->bind($request);

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
        $editForm->bind($request);

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
        if ($exist == true) 
        {
            $this->get('session')->getFlashBag()->add('error', 'Suppression impossible : le contact <strong>'. $people->getEmail() .'</strong>, est utilisé dans les équipements.');
        } 
        else
        {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer le contact <strong>'.$people->getEmail().'</strong> ?';
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
        $em = $this->getDoctrine()->getEntityManager();
        $peopleRepo = $em->getRepository('CSISEamBundle:People');
        $input = $request->request->get('input');
        
        if ( $this->getRequest()->isXmlHttpRequest() ) {
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
}
