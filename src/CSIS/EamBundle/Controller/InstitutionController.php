<?php

namespace CSIS\EamBundle\Controller;

use CSIS\EamBundle\Form\InstitutionEditOwnersType;
use CSIS\EamBundle\Form\LaboratoryEditOwnersType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
 * @Route("/institutions")
 */
class InstitutionController extends Controller {

    /**
     * Lists all Institution entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Institution:index.html.twig")
     * @Route("/{page}", name="institution", requirements={"page" = "\d+"}, defaults={"page" = 1})
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine();

        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $user = $this->getUser();
        $institutions = $em->getRepository('CSISEamBundle:Institution')->findByOwnerOrderByAcronymPaginated($user, $page, $maxPerPage);

        return array(
            'institutions' => $institutions,
            'page' => $page,
            'nbPages' => ceil(count($institutions) / $maxPerPage)
        );
    }

    /**
     * Finds and displays a Institution entity.
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     * @Template("CSISEamBundle:Institution:show.html.twig")
     * @Route("/{id}/show", name="institution_show", requirements={"id" = "\d+"})
     */
    public function showAction(Institution $institution)
    {
        return array('institution' => $institution);
    }

    /**
     * Displays a form to create a new Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Template("CSISEamBundle:Institution:new.html.twig")
     * @Route("/new", name="institution_new")
     */
    public function newAction()
    {
        $institution = new Institution();
        $form = $this->createForm(new InstitutionType(), $institution);
        return array(
            'institution' => $institution,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Template("CSISEamBundle:Institution:new.html.twig")
     * @Route("/create", name="institution_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $institution = new Institution();

        $form = $this->createForm(new InstitutionType(), $institution);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $institution->getOwners()->add($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($institution);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Etablissement ajouté !');
            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }

        return array(
            'institution' => $institution,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Template("CSISEamBundle:Institution:edit.html.twig")
     * @Route("/{id}/edit", name="institution_edit", requirements={"id" = "\d+"})
     */
    public function editAction(Institution $institution)
    {
        $editForm = $this->createForm(new InstitutionType(), $institution);

        return array(
            'institution' => $institution,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Institution entity.
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Method({"POST"})
     * @Route("/{id}/update", name="institution_update", requirements={"id" = "\d+"})
     * @Template("CSISEamBundle:Institution:edit.html.twig")
     */
    public function updateAction(Institution $institution, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new InstitutionType(), $institution);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($institution);
            $em->flush();

            /* Creation auto des tags */
            // @TODO: WTF ???
            // $repo = $em->getRepository('CSISEamBundle:Tag');
            // $repo->AddTag($institution->getAcronym()." ".$institution->getName());

            $this->get('session')->getFlashBag()->add('valid', 'Etablissement modifié !');
            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }

        return array(
            'institution' => $institution,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Route("/{id}/ask_delete", name="institution_ask_delete", requirements={"id" = "\d+"})
     */
    public function askDeleteAction(Institution $institution)
    {
        $repo = $this->getDoctrine()->getRepository('CSISEamBundle:Laboratory');

        // On vérifie si l'Etablissement existe
        if ($repo->isInstitutionUsed($institution)) {
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
     * @Route("/{id}/delete", name="institution_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction(Institution $institution)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($institution);
        $em->flush();
        $this->get('session')->getFlashBag()->add('valid', 'Etablissement supprimé !');

        return $this->redirect($this->generateUrl('institution'));
    }

    /**
     * Manage institution owners
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Route("/{id}/credentials", name="institution_credentials", requirements={"id" = "\d+"})
     * @Template("CSISEamBundle:Institution:credentials.html.twig")
     */
    public function credentialsAction(Institution $institution)
    {
        return array('institution' => $institution);
    }

    /**
     * Remove an owner from an institution
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Route("/{id}/credentials/{owner}/remove", name="institution_credentials_remove", requirements={"id" = "\d+", "owner" = "\d+"})
     */
    public function credentialsRemoveAction(Institution $institution, User $owner)
    {
        $em = $this->getDoctrine()->getManager();
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
     * @Route("/{id}/credentials/edit", name="institution_credentials_edit", requirements={"id" = "\d+"})
     * @Template("CSISEamBundle:Institution:editOwners.html.twig")
     */
    public function credentialsEditAction(Institution $institution, Request $request)
    {
        $form = $this->createForm(new InstitutionEditOwnersType($this->getUser()), $institution);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('institution_credentials', array('id' => $institution->getId())));
        }

        return array(
            'institution' => $institution,
            'form' => $form->createView(),
        );
    }

    /**
     * Make a fusion between multiple institutions
     * @Secure(roles="ROLE_GEST_ESTAB")
     * @Route("/{id}/fusion", name="institution_fusion", requirements={"id" = "\d+"})
     * @Template("CSISEamBundle:Institution:fusion.html.twig")
     */
    public function fusionAction(Institution $institution, Request $request)
    {
        $fusionClass = new InstitutionFusionClass();
        $form = $this->createForm(new InstitutionFusionType(), $fusionClass);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $fusionClass->fusionWith($institution);

            $em = $this->getDoctrine()->getManager();
            foreach ($fusionClass->getInstitutions() as $inst) {
                $em->remove($inst);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('institution_show', array('id' => $institution->getId())));
        }

        return array(
            'institution' => $institution,
            'form' => $form->createView(),
        );
    }
}
