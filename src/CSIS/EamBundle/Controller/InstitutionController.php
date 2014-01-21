<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Classes\InstitutionFusionClass;
use CSIS\EamBundle\Entity\Institution;
use CSIS\EamBundle\Form\InstitutionType;
use CSIS\EamBundle\Form\InstitutionFusionType;
use CSIS\EamBundle\Form\InstitutionAddOwnerType;
use CSIS\UserBundle\Entity\User;


/**
 * Institution controller.
 *
 */
class InstitutionController extends Controller {

    /**
     * Lists all Institution entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function indexAction($page) {
        $em = $this->getDoctrine()->getManager();

        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $user = $this->getUser();
        $institutions = $em->getRepository('CSISEamBundle:Institution')->findByOwnerOrderByAcronymPaginated($user, $page, $maxPerPage);

        return $this->render('CSISEamBundle:Institution:index.html.twig', array(
                    'institutions' => $institutions,
                    'page' => $page,
                    'nbPages' => ceil(count($institutions) / $maxPerPage),
        ));
    }

    /**
     * Finds and displays a Institution entity.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function showAction(Institution $institution) {

        return $this->render('CSISEamBundle:Institution:show.html.twig', array(
                    'institution' => $institution,
        ));
    }

    /**
     * Displays a form to create a new Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function newAction() {
        $institution = new Institution();
        $form = $this->createForm(new InstitutionType(), $institution);

        return $this->render('CSISEamBundle:Institution:new.html.twig', array(
                    'institution' => $institution,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function createAction(Request $request) {
        $institution = new Institution();

        $form = $this->createForm(new InstitutionType(), $institution);
        $form->bind($request);

        if ($form->isValid()) {
            $institution->getOwners()->add($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($institution);
            $em->flush();
            
            /* Creation auto des tags */
            $repo = $em->getRepository('CSISEamBundle:Tag');
            $repo->AddTag($institution->getAcronym()." ".$institution->getName());

            $this->get('session')->getFlashBag()->add('valid', 'Etablissement ajouté !');
            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }

        return $this->render('CSISEamBundle:Institution:new.html.twig', array(
                    'institution' => $institution,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function editAction(Institution $institution) {

        $editForm = $this->createForm(new InstitutionType(), $institution);

        return $this->render('CSISEamBundle:Institution:edit.html.twig', array(
                    'institution' => $institution,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function updateAction(Request $request, Institution $institution) {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new InstitutionType(), $institution);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($institution);  
            $em->flush();

            /* Creation auto des tags */
            $repo = $em->getRepository('CSISEamBundle:Tag');
            $repo->AddTag($institution->getAcronym()." ".$institution->getName());
            
            $this->get('session')->getFlashBag()->add('valid', 'Etablissement modifié !');
            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }

        return $this->render('CSISEamBundle:Institution:edit.html.twig', array(
                    'institution' => $institution,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function askDeleteAction(Institution $institution) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CSISEamBundle:Institution');

        // On vérifie si l'Etablissement existe
        $exist = $repo->isInstitutionUsed($institution->getId());
        if ($exist == true) {
            $this->get('session')->getFlashBag()->add('error', 'Suppression impossible : l\'établissement <strong>' . $institution->getAcronym() . '</strong>, est utilisé dans les laboratoires.');
        } else {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer l\'établissement <strong>' . $institution->getAcronym() . '</strong> ?';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('institution_delete', array('id' => $institution->getId())) . '">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('institution') . '">Non</a>';
            $this->get('session')->getFlashBag()->add('main_valid', $message);
        }
        // Redirection vers la page principale
        return $this->redirect($this->generateUrl('institution'));
    }

    /**
     * Deletes a Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     */
    public function deleteAction(Institution $institution) {

        $em = $this->getDoctrine()->getManager();

        if (!$institution) {
            throw $this->createNotFoundException('Unable to find Institution entity.');
        }

        $em->remove($institution);
        $em->flush();
        $this->get('session')->getFlashBag()->add('valid', 'Etablissement supprimé !');

        return $this->redirect($this->generateUrl('institution'));
    }

    /**
     * Manage institution owners
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @param \CSIS\EamBundle\Entity\Institution $institution
     */
    public function credentialsAction(Institution $institution) {
        
        return $this->render('CSISEamBundle:Institution:credentials.html.twig', array(
            'institution' => $institution,
            'owners' => $institution->getOwners(),
        ));
    }
    
    /**
     * Remove an owner from an institution
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @param \CSIS\EamBundle\Entity\Institution $institution
     * @param \CSIS\UserBundle\Entity\User $owner
     */
    public function credentialsRemoveAction(Institution $institution, User $owner) {
        $em = $this->getDoctrine()->getEntityManager();
        
        if( $institution->getOwners()->contains($owner) ) {
            $institution->getOwners()->removeElement($owner);
            $this->container->get('session')->getFlashBag()->set('main_valid', 
                    'Propriétaire '. $owner->getLastName() . ' ' . $owner->getFirstName() .' supprimé.'
            );
        } else {
            $this->container->get('session')->getFlashBag()->set('main_error', 
                    'Erreur lors de la supression du propriétaire '. $owner->getLastName() . ' ' . $owner->getFirstName() .'.'
            );
        }
        
        $em->flush();
        
        return $this->redirect($this->generateUrl('institution_credentials', array('id' => $institution->getId())));
    }
    
    /**
     * Add an owner to an institution
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \CSIS\EamBundle\Entity\Institution $institution
     */
    public function credentialsAddAction(Request $request, Institution $institution) {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CSISUserBundle:User');
        
        $form = $this->createForm(new InstitutionAddOwnerType(), $institution);
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $form->bind($request);
            if ( $form->isValid() ) {
                $em->flush();
                return $this->redirect($this->generateUrl('institution_credentials', array('id' => $institution->getId())));
            }
        }
        
        return $this->render('CSISEamBundle:Institution:addOwner.html.twig', array(
            'institution' => $institution,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Make a fusion between multiple institutions
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @param \CSIS\EamBundle\Entity\Institution $institution
     */
    public function fusionAction(Institution $institution) {
        $em = $this->getDoctrine()->getEntityManager();
        $fusionClass = new InstitutionFusionClass();
        $form = $this->createForm(new InstitutionFusionType(), $fusionClass);
        
        if ( $this->getRequest()->isMethod('POST') ) {
            $form->bind($this->getRequest());
            
            $fusionClass->fusionWith($institution);
            
            foreach ($fusionClass->getInstitutions() as $inst) $em->remove($inst);
            $em->flush();
            
            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }
        
        return $this->render('CSISEamBundle:Institution:fusion.html.twig', array(
            'institution' => $institution,
            'form' => $form->createView(),
        ));
    }
    
}
