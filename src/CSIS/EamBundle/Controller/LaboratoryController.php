<?php

namespace CSIS\EamBundle\Controller;

use CSIS\EamBundle\Form\LaboratoryEditOwnersType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use CSIS\EamBundle\Entity\Laboratory;
use CSIS\EamBundle\Classes\LaboratoryFusionClass;
use CSIS\EamBundle\Form\LaboratoryType;
use CSIS\EamBundle\Form\LaboratoryAddOwnerType;
use CSIS\EamBundle\Form\LaboratoryFusionType;
use CSIS\UserBundle\Entity\User;

/**
 * Laboratory controller.
 * @Route("/laboratories")
 */
class LaboratoryController extends Controller
{
    /**
     * Lists all Laboratory entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Laboratory:index.html.twig")
     * @Route("/", name="laboratory")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $laboratories = $em->getRepository('CSISEamBundle:Laboratory')->findByOwnerOrderByAcronym($user);

        return array(
            'laboratories' => $laboratories,
        );
    }

    /**
     * Finds and displays a Laboratory entity.
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     * @Template("CSISEamBundle:Laboratory:show.html.twig")
     * @Route("/{id}/show", name="laboratory_show", requirements={"id" = "\d+"})
     */
    public function showAction(Laboratory $laboratory)
    {
        $deleteForm = $this->createDeleteForm($laboratory->getId());

        $members = $this->getDoctrine()->getRepository('CSISUserBundle:User')
            ->createQueryBuilder('u')
            ->where('u.lab = :lab')
            ->setParameter('lab', $laboratory)
            ->orderBy('u.lastName')
            ->getQuery()
            ->getResult();

        $responsables = $this->getDoctrine()->getRepository('CSISUserBundle:User')
            ->createQueryBuilder('u')
            ->where('u.lab = :lab')
            ->setParameter('lab', $laboratory)
            ->andWhere('u.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_GEST_EQUIP%')
            ->orderBy('u.lastName')
            ->getQuery()
            ->getResult();

        return array(
            'laboratory' => $laboratory,
            'delete_form' => $deleteForm->createView(),
            'members' => $members,
            'responsables' => $responsables,
        );
    }

    /**
     * Displays a form to create a new Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:new.html.twig")
     * @Route("/new", name="laboratory_new")
     */
    public function newAction()
    {
        $laboratory = new Laboratory();
        $form = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);

        return array(
            'laboratory' => $laboratory,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:new.html.twig")
     * @Route("/create", name="laboratory_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $laboratory = new Laboratory();
        $form = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $laboratory->getOwners()->add($this->getUser());

            $em->persist($laboratory);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Laboratoire ajouté !');
            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $laboratory->getId())));
        }

        return array(
                    'laboratory' => $laboratory,
                    'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:edit.html.twig")
     * @Route("/{id}/edit", name="laboratory_edit", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function editAction(Laboratory $laboratory)
    {
        $editForm = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $deleteForm = $this->createDeleteForm($laboratory->getId());

        return array(
            'laboratory' => $laboratory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:edit.html.twig")
     * @Route("/{id}/update", name="laboratory_update", requirements={"id" = "\d+"})
     * @Method({"POST"})
     */
    public function updateAction(Request $request, Laboratory $laboratory)
    {
        $deleteForm = $this->createDeleteForm($laboratory->getId());
        $editForm = $this->createForm(new LaboratoryType($this->getUser()), $laboratory);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($laboratory);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Laboratoire modifié !');
            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $laboratory->getId())));
        }

        return array(
                    'laboratory' => $laboratory,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Secure(roles="ROLE_GEST_LAB")
     * @Route("/{id}/ask_delete", name="laboratory_ask_delete", requirements={"id" = "\d+"})
     */
    public function askDeleteAction(Laboratory $laboratory)
    {
        $repo = $this->getDoctrine()->getRepository('CSISEamBundle:Equipment');

        // On vérifie si le laboratoire existe
        if ($repo->isLaboratoryUsed($laboratory)) {
            $this->addFlash(
                'error',
                sprintf("Suppression impossible : le laboratoire <strong> %s </strong>, est utilisé dans les équipements.", $laboratory->getAcronym())
            );
        } else {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer le laboratoire <strong>'.$laboratory->getAcronym().'</strong> ?';
            $message .= '&nbsp;&nbsp<a href="'.$this->generateUrl('laboratory_delete', array('id'=>$laboratory->getId())).'">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="'.$this->generateUrl('laboratory').'">Non</a>';
            $this->addFlash('main_valid',  $message);
        }

        // Redirection vers la page principale
        return $this->redirectToRoute('laboratory');
    }

    /**
     * Deletes a Laboratory entity.
     * @Secure(roles="ROLE_GEST_LAB")
     * @Route("/{id}/delete", name="laboratory_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction(Laboratory $laboratory) {

        $em = $this->getDoctrine()->getManager();
        $em->remove($laboratory);
        $em->flush();
        $this->get('session')->getFlashBag()->add('valid', 'Laboratoire supprimé !');

        return $this->redirect($this->generateUrl('laboratory'));
    }

    /**
     * Manage laboratory owners
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:credentials.html.twig")
     * @Route("/{id}/credentials", name="laboratory_credentials", requirements={"id" = "\d+"})
     */
    public function credentialsAction(Laboratory $laboratory)
    {
        return array(
            'laboratory' => $laboratory,
            'owners' => $laboratory->getOwners(),
        );
    }

    /**
     * Remove an owner from a laboratory
     * @Secure(roles="ROLE_GEST_LAB")
     * @Route("/{id}/credentials/{owner}/remove", name="laboratory_credentials_remove", requirements={"id" = "\d+", "owner" = "\d+"})
     */
    public function credentialsRemoveAction(Laboratory $laboratory, User $owner)
    {
        $em = $this->getDoctrine()->getManager();

        if($laboratory->getOwners()->contains($owner)) {
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
     * Edit owners of a laboratory
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:editOwners.html.twig")
     * @Route("/{id}/credentials/edit", name="laboratory_credentials_edit", requirements={"id" = "\d+"})
     */
    public function credentialsEditAction(Request $request, Laboratory $laboratory)
    {
        $form = $this->createForm(new LaboratoryEditOwnersType($this->getUser()), $laboratory);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirect($this->generateUrl('laboratory_credentials', array('id' => $laboratory->getId())));
        }

        return array(
            'laboratory' => $laboratory,
            'form' => $form->createView(),
        );
    }

    /**
     * Make a fusion between multiple laboratories
     * @Secure(roles="ROLE_GEST_LAB")
     * @Template("CSISEamBundle:Laboratory:fusion.html.twig")
     * @Route("/{id}/fusion", name="laboratory_fusion", requirements={"id" = "\d+"})
     */
    public function fusionAction(Laboratory $laboratory, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $labClass = new LaboratoryFusionClass();
        $form = $this->createForm(new LaboratoryFusionType(), $labClass);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $labClass->fusionWith($laboratory);

            foreach ($labClass->getLaboratories() as $lab) {
                $em->remove($lab);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('laboratory_show', array('id' => $laboratory->getId())));
        }

        return array(
            'lab' => $laboratory,
            'form' => $form->createView(),
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }
}
