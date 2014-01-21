<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use CSIS\EamBundle\Entity\Laboratory;
use CSIS\EamBundle\Classes\LaboratoryFusionClass;
use CSIS\EamBundle\Form\LaboratoryType;
use CSIS\EamBundle\Form\LaboratoryAddOwnerType;
use CSIS\EamBundle\Form\LaboratoryFusionType;
use CSIS\UserBundle\Entity\User;

/**
 * Laboratory controller.
 *
 */
class LaboratoryController extends Controller {

    /**
     * Lists all Laboratory entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();

        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $user = $this->getUser();
        
        $laboratories = $em->getRepository('CSISEamBundle:Laboratory')->findByOwnerOrderByAcronymPaginated($user, $page, $maxPerPage);

        return $this->render('CSISEamBundle:Laboratory:index.html.twig', array(
                    'laboratories' => $laboratories,
                    'page' => $page,
                    'nbPages' => ceil(count($laboratories) / $maxPerPage),
        ));
    }

    /**
     * Finds and displays a Laboratory entity.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $laboratory = $em->getRepository('CSISEamBundle:Laboratory')->find($id);

        if (!$laboratory) {
            throw $this->createNotFoundException('Unable to find Laboratory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:Laboratory:show.html.twig', array(
                    'laboratory' => $laboratory,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to create a new Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function newAction() {
        $laboratory = new Laboratory();
        $form = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);

        return $this->render('CSISEamBundle:Laboratory:new.html.twig', array(
                    'laboratory' => $laboratory,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function createAction(Request $request) {
        $laboratory = new Laboratory();
        $form = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $laboratory->getOwners()->add($this->getUser());
            
            $em->persist($laboratory);
            $em->flush();
            
            /* Creation auto des tags */
            $repo = $em->getRepository('CSISEamBundle:Tag');
            $repo->AddTag($laboratory->getAcronym()." ".$laboratory->getNameFr()." ".$laboratory->getNameEn());

            $this->get('session')->getFlashBag()->add('valid', 'Laboratoire ajouté !');
            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $laboratory->getId())));
        }

        return $this->render('CSISEamBundle:Laboratory:new.html.twig', array(
                    'laboratory' => $laboratory,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $laboratory = $em->getRepository('CSISEamBundle:Laboratory')->find($id);

        if (!$laboratory) {
            throw $this->createNotFoundException('Unable to find Laboratory entity.');
        }

        $editForm = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:Laboratory:edit.html.twig', array(
                    'laboratory' => $laboratory,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $laboratory = $em->getRepository('CSISEamBundle:Laboratory')->find($id);

        if (!$laboratory) {
            throw $this->createNotFoundException('Unable to find Laboratory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($laboratory);
            $em->flush();
            
            /* Creation auto des tags */
            $repo = $em->getRepository('CSISEamBundle:Tag');
            $repo->AddTag($laboratory->getAcronym()." ".$laboratory->getNameFr()." ".$laboratory->getNameEn());

            $this->get('session')->getFlashBag()->add('valid', 'Laboratoire modifié !');
            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $id)));
        }

        return $this->render('CSISEamBundle:Laboratory:edit.html.twig', array(
                    'laboratory' => $laboratory,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }
    
     /**
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function askDeleteAction(Laboratory $laboratory) {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CSISEamBundle:Laboratory');
        
        // On vérifie si le laboratoire existe
        $exist = $repo->isLaboratoryUsed($laboratory->getId());
        if ($exist == true) 
        {
            $this->get('session')->getFlashBag()->add('error', 'Suppression impossible : le laboratoire <strong>'. $laboratory->getAcronym() .'</strong>, est utilisé dans les équipements.');
        } 
        else
        {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer le laboratoire <strong>'.$laboratory->getAcronym().'</strong> ?';
            $message .= '&nbsp;&nbsp<a href="'.$this->generateUrl('laboratory_delete', array('id'=>$laboratory->getId())).'">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="'.$this->generateUrl('laboratory').'">Non</a>';
            $this->get('session')->getFlashBag()->add('main_valid',  $message);
        }
        // Redirection vers la page principale
        return $this->redirect($this->generateUrl('laboratory'));
    }
    
    /**
     * Deletes a Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     */
    public function deleteAction(Laboratory $laboratory) {

        $em = $this->getDoctrine()->getManager();

        if (!$laboratory) {
            throw $this->createNotFoundException('Unable to find Laboratory entity.');
        }

        $em->remove($laboratory);
        $em->flush();
        $this->get('session')->getFlashBag()->add('valid', 'Laboratoire supprimé !');

        return $this->redirect($this->generateUrl('laboratory'));
    }
    
    /**
     * Manage laboratory owners
     * @Secure(roles="ROLE_GEST_LAB")
     * @param \CSIS\EamBundle\Entity\Laboratory $institution
     */
    public function credentialsAction(Laboratory $laboratory) {
        
        return $this->render('CSISEamBundle:Laboratory:credentials.html.twig', array(
            'laboratory' => $laboratory,
            'owners' => $laboratory->getOwners(),
        ));
    }
    
    /**
     * Remove an owner from a laboratory
     * @Secure(roles="ROLE_GEST_LAB")
     * @param \CSIS\EamBundle\Entity\Laboratory $institution
     * @param \CSIS\UserBundle\Entity\User $owner
     */
    public function credentialsRemoveAction(Laboratory $laboratory, User $owner) {
        $em = $this->getDoctrine()->getEntityManager();
        
        if( $laboratory->getOwners()->contains($owner) ) {
            $laboratory->getOwners()->removeElement($owner);
            $this->container->get('session')->getFlashBag()->set('main_valid', 
                    'Propriétaire '. $owner->getLastName() . ' ' . $owner->getFirstName() .' supprimé.'
            );
        } else {
            $this->container->get('session')->getFlashBag()->set('main_error', 
                    'Erreur lors de la supression du propriétaire '. $owner->getLastName() . ' ' . $owner->getFirstName() .'.'
            );
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('laboratory_credentials', array('id' => $laboratory->getId())));
    }
    
    /**
     * Add an owner to a laboratory
     * @Secure(roles="ROLE_GEST_LAB")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \CSIS\EamBundle\Entity\Laboratory $institution
     */
    public function credentialsAddAction(Request $request, Laboratory $laboratory) {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CSISUserBundle:User');
        
        $form = $this->createForm(new LaboratoryAddOwnerType($this->getUser()), $laboratory);
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $form->bind($request);
            if ( $form->isValid() ) {
                $em->flush();
                return $this->redirect($this->generateUrl('laboratory_credentials', array('id' => $laboratory->getId())));
            }
        }
        
        return $this->render('CSISEamBundle:Laboratory:addOwner.html.twig', array(
            'laboratory' => $laboratory,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Make a fusion between multiple laboratories
     * @Secure(roles="ROLE_GEST_LAB")
     * @param \CSIS\EamBundle\Entity\Laboratory $laboratory
     */
    public function fusionAction(Laboratory $laboratory) {
        $em = $this->getDoctrine()->getEntityManager();
        $labClass = new LaboratoryFusionClass();
        $form = $this->createForm(new LaboratoryFusionType(), $labClass);
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $form->bind($this->getRequest());
            
            $labClass->fusionWith($laboratory);
            
            foreach ($labClass->getLaboratories() as $lab) $em->remove($lab);
            $em->flush();
            
            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $laboratory->getId())));
        }
        
        return $this->render('CSISEamBundle:Laboratory:fusion.html.twig', array(
            'lab' => $laboratory,
            'form' => $form->createView(),
        ));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
