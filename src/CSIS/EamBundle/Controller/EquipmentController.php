<?php

namespace CSIS\EamBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Entity\Tag;
use CSIS\EamBundle\Form\EquipmentType;
use CSIS\EamBundle\Form\EquipmentAddOwnerType;
use CSIS\UserBundle\Entity\User;
use CSIS\EamBundle\Entity\EquipmentTag;

/**
 * This Controller allows to manage all actions of the Equipement entity
 *
 * @Route("/equipments")
 */
class EquipmentController extends Controller
{
    /**
     * Displays all reachable Equipments to the user
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:index.html.twig")
     * @Route("/", name="equipment")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('CSISEamBundle:Equipment');
        $user = $this->getUser();
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $page = $request->query->get('page', 1);

        $entities = $repo->findByOwnersOrderByDesignationPaginated($user, $page, $maxPerPage);

        return array(
            'entities' => $entities,
            'page' => $page,
            'nbPages' => ceil(count($entities) / $maxPerPage),
        );
    }

    /**
     * Displays the requested Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}", name="equipment_show", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template("CSISEamBundle:Equipment:show.html.twig")
     */
    public function showAction(Equipment $equipment)
    {
        return array('equipment' => $equipment);
    }

    /**
     * Displays a form for Equipment creation
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:new.html.twig")
     * @Route("/new", name="equipment_new")
     * @Method({"GET"})
     */
    public function newAction()
    {
        $equipment = new Equipment();

        $equipment->getOwners()->add($this->getUser());
        $form = $this->createForm(new EquipmentType($this->getUser()), $equipment, array('summary' => true));

        return array(
            'equipment' => $equipment,
            'form' => $form->createView(),
        );
    }

    /**
     * Receives the POST data in order to create an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:new.html.twig")
     * @Route("/new", name="equipment_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $equipment = new Equipment();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new EquipmentType($this->getUser()), $equipment, array('summary' => true));

        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @TODO: Handle here the owners */

            $em->persist($equipment);
            $em->flush();

            /* Creation auto des liens avec les tags */
            $this->attachTags($equipment);

            $this->addFlash(
                    'valid', sprintf('
                        L\'équipement %s a été ajouté ! Des tags on été ' .
                            'ajoutés automatiquement vous pourrez les modifier.', $equipment->getDesignation()
                    )
            );

            return $this->redirect($this->generateUrl('equipment_show', array( 'id' => $equipment->getId() )));
        }

        return array(
            'equipment' => $equipment,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form for Equipment edition
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:edit.html.twig")
     * @Route("/{id}/edit", name="equipment_edit", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function editAction(Equipment $equipment)
    {
        if ($equipment->getOwners()->isEmpty()) {
            $equipment->getOwners()->add(new User());
        }

        $editForm = $this->createForm(new EquipmentType($this->getUser()), $equipment, array('summary' => true));

        return array(
            'equipment' => $equipment,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Displays a widget for tags edition for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:editTags.html.twig")
     * @Route("/{id}/edit/tags", name="equipment_edit_tags", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editTagsAction(Equipment $equipment, Request $request)
    {
        $form = $this->createForm(new EquipmentType($this->getUser()), $equipment, array('tags' => true));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Les tags ont correctement été mis à jour.');
        }

        return array(
            'equipment' => $equipment,
            'form' => $form->createView(),
        );
    }

    /**
     * Receives the POST data in order to edit an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Equipment:edit.html.twig")
     * @Route("/{id}/edit", name="equipment_update", requirements={"id" = "\d+"})
     * @Method({"POST"})
     */
    public function updateAction(Request $request, Equipment $equipment)
    {
        $editForm = $this->createForm(new EquipmentType($this->getUser()), $equipment, array('summary' => true));

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($equipment);
            $em->flush();

            $this->addFlash(
                'valid',
                sprintf('La mise à jour de votre équipement %s est bien prise en compte.', $equipment->getDesignation())
            );

            return $this->redirect($this->generateUrl('equipment_show', array('id' => $equipment->getId())));
        }

        return array(
            'equipment' => $equipment,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Displays to the user a message in order to confirm the removal of the Equipment entity
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}/ask_delete", name="equipment_ask_delete", requirements={"id" = "\d+"})
     */
    public function askDeleteAction( Equipment $entity )
    {
        // Message de confirmation
        $this->addFlash(
                'valid', sprintf('Etes-vous sûr de bien vouloir supprimer l\'équipement <strong>%1$s</strong> ?'
                        . '&nbsp;&nbsp<a href="%2$s">Oui</a>&nbsp;/&nbsp;<a href="%3$s">Non</a>', $entity->getDesignation(), $this->generateUrl('equipment_delete', array( 'id' => $entity->getId() )), $this->generateUrl('equipment')
                )
        );
        // Redirection vers la page principale
        return $this->redirect($this->generateUrl('equipment'));
    }

    /**
     * Remove the requested Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}/delete", name="equipment_delete", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function deleteAction(Equipment $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($entity);
        $em->flush();

        $this->addFlash(
                'valid', sprintf('Equipement <strong>%s</strong> supprimé !', $entity->getDesignation())
        );

        return $this->redirect($this->generateUrl('equipment'));
    }

    /**
     * Fetch tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}/edit/tags/fetch", name="equipment_fetch_tags", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function fetchTagsAction(Equipment $equipment, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('The page you are requested is only avalaible by ajax requests');
        }

        $equipmentTags = $equipment->getEquipmentTags();

        $json = array('accepted' => array(), 'rejected' => array());

        foreach ($equipmentTags as $equipmentTag)
        {
            if ($equipmentTag->getStatus() == EquipmentTag::ACCEPTED)
            {
                $json['accepted'][] = array('name' => $equipmentTag->getTag()->getTag(), 'status' => $equipmentTag->getTag()->getStatus());
            }
            elseif ($equipmentTag->getStatus() == EquipmentTag::REFUSED)
            {
                $json['rejected'][] = array('name' => $equipmentTag->getTag()->getTag(), 'status' => $equipmentTag->getTag()->getStatus());
            }
        }

        return new JsonResponse($json);
    }

    /**
     * Fetch relative tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}/edit/tags/relatives", name="equipment_fetch_relative_tags", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function fetchRelativeTagsAction(Equipment $equipment, Request $request)
    {
        if (!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('The page you are requested is only avalaible by ajax requests');

        $tags = $equipment->getTags();

        // On attache les pseudos tags
        $this->attachTags($equipment, false);

        // Fetch associated Tags from DB
        $newTags = $this->getDoctrine()->getRepository('CSISEamBundle:Tag')->findRelativeTags($equipment->getTags());

        $relativesTag = array();
        foreach ( $newTags as $tag ) {
            if ( !$tags->contains($tag['tag']) ) {
                $relativesTag[] = array('name' => $tag['tag']->getTag(), 'status' => $tag['tag']->getStatus());
            }
        }

        $json = array('suggested' => $relativesTag);

        return new JsonResponse($json);
    }

    /**
     * Update tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Route("/{id}/edit/tags/update", name="equipment_update_tags", requirements={"id" = "\d+"})
     * @Method({"POST"})
     */
    public function updateTagsAction(Equipment $equipment, Request $request)
    {
        if (!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('The page you are requested is only avalaible by ajax requests');

        $em = $this->getDoctrine()->getManager();
        $tagRepo = $em->getRepository('CSISEamBundle:Tag');

        $tags = $request->request->get('tags');

        // 1. On supprime tous les tags de l'équipement
        $oldEquipmentTags = $equipment->getEquipmentTags();
        foreach ($oldEquipmentTags as $equipmentTag)
        {
            $equipment->removeEquipmentTag($equipmentTag);
            $equipmentTag->setTag(null);
            $equipmentTag->setEquipment(null);
        }

        // 2. On traite les tags reçus en POST
        foreach ($tags as $tag => $status)
        {
            $existingTag = $tagRepo->findOneByTag($tag);

            // Si le tag n'existe pas
            if(!$existingTag)
            {
                $existingTag = new Tag();
                $existingTag->setTag($tag);
                $existingTag->setStatus(false);
                $existingTag->setLastEditDate(new \DateTime());

            }

            $equipmentTag = new EquipmentTag();
            $equipmentTag->setEquipment($equipment);
            $equipmentTag->setTag($existingTag);

            if ($status == 'accepted')
            {
                $equipmentTag->setStatus(EquipmentTag::ACCEPTED);
            }
            elseif ($status == 'rejected')
            {
                $equipmentTag->setStatus(EquipmentTag::REFUSED);
            }
            else
            {
                var_dump($status); die;
            }

            $em->persist($existingTag);
            $em->persist($equipmentTag);
            $em->persist($equipment);
        }

        $em->flush();

        return new JsonResponse(array('message' => 'ok'));
    }

    /**
     * Scan the Equipment and attach tags to it
     *
     * @param \CSIS\EamBundle\Entity\Equipment $equipment
     */
    protected function attachTags( Equipment $equipment, $andFlush = true )
    {
        $lab = $equipment->getLaboratory();
        $inst = $lab->getInstitution();

        // Def des tag a recuperer
        $words = implode(' ', array(
            $equipment->getDesignation(),
            $equipment->getBrand(),
            $equipment->getBrand(),
            $equipment->getType(),
            $lab->getAcronym(),
            $lab->getNameFr(),
            $lab->getNameEn(),
            $inst->getAcronym(),
            $inst->getName(),
        ));

        // Suppression des elements a ne pas traiter
        $words = str_replace('à', 'a', $words);

        $commomWordsFilter = '#\b[a]\b|\baux?\b|\b\bo[ùu]\b|\bd[eu]s?\b|\bsur\b|\bpar\b|\ben\b|\bpour\b|\bl[ae]\b|\bet\b|\bsous\b|\bn°\d+\b|\b\d+\b#i';
        $words = preg_replace($commomWordsFilter, '', $words);

        $punctuationFilter = '#[-(){}\[\]\\/+*.,;:!?_&@=~\']| [a-zA-Z]{1}\'#';
        $words = preg_replace($punctuationFilter, '', $words);

        // Decomposition de la chaine en tableau de mots
        $tagList = explode(' ', $words);

        // Recuperation du repository des tags
        $em = $this->getDoctrine()->getManager();
        $repoTag = $em->getRepository('CSISEamBundle:Tag');

        // Definition de la liste des tags existant
        foreach ( $tagList as $tagVal ) {
            if ( !empty($tagVal) ) {
                // On verifi que le tag n'existe pas deja
                $tag = $repoTag->findOneByTag($tagVal);
                if ( (null !== $tag) && (false === $equipment->getTags()->contains($tag)) ) {
                    $equipment->getTags()->add($tag);
                }
            }
        }

        if ( $andFlush ) {
            // Application des tags existant
            $em->persist($equipment);
            $em->flush();
        }
    }

    /**
     * ShortMethod to add flashes to the session
     * @param string $type    The type name for flashbag
     * @param string $message The message of the flash
     */
    private function addFlash( $type, $message )
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }
}
