<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpFoundation\Response
;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Equipment,
    CSIS\EamBundle\Entity\People,
    CSIS\EamBundle\Entity\Tag,
    CSIS\EamBundle\Form\EquipmentType,
    CSIS\EamBundle\Form\EquipmentAddOwnerType,
    CSIS\UserBundle\Entity\User,
    CSIS\EamBundle\Entity\EquipmentTag

;

/**
 * This Controller allows to manage all actions of the Equipement entity
 * 
 */
class EquipmentController extends Controller
{

    /**
     * Displays all reachable Equipments to the user
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction( Request $request )
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('CSISEamBundle:Equipment');
        $user = $this->getUser();
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $page = $request->query->get('page', 1);

        $entities = $repo->findByOwnersOrderByDesignationPaginated($user, $page, $maxPerPage);

        return $this->render('CSISEamBundle:Equipment:index.html.twig', array(
                    'entities' => $entities,
                    'page' => $page,
                    'nbPages' => ceil(count($entities) / $maxPerPage),
        ));
    }

    /**
     * Displays the requested Equipment
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipment the equipment to display
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction( Equipment $equipment )
    {
        return $this->render('CSISEamBundle:Equipment:show.html.twig', array(
                    'equipment' => $equipment,
        ));
    }

    /**
     * Displays a form for Equipment creation
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $equipment = new Equipment();
        $equipment->getContacts()->add(new People()); // Enlever le label
        $equipment->getTags()->add(new Tag()); // Enlever le label
        $form = $this->createForm(new EquipmentType($this->getUser()), $equipment);

        return $this->render('CSISEamBundle:Equipment:new.html.twig', array(
                    'equipment' => $equipment,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Receives the POST data in order to create an Equipment 
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\ResdirectResponse
     */
    public function createAction( Request $request )
    {
        $equipment = new Equipment();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new EquipmentType($this->getUser()), $equipment);

        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $equipment->getOwners()->add($this->getUser());

                $em->persist($equipment);
                $em->flush();

                /* Creation auto des liens avec les tags */
                $this->attachTags($equipment);

                $this->addFlash(
                        'valid', sprintf('
                            L\'équipement %s a été ajouté ! Des tags on été ' .
                                'ajoutés automatiquement vous pourrez les modifier', $equipment->getDesignation()
                        )
                );
                return $this->redirect($this->generateUrl('equipment_show', array( 'id' => $equipment->getId() )));
            }
        }

        return $this->render('CSISEamBundle:Equipment:new.html.twig', array(
                    'equipment' => $equipment,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form for Equipment edition
     *  
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipement The Equipment to edit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction( Equipment $equipment )
    {
        if ( $equipment->getContacts()->isEmpty() ) {
            $equipment->getContacts()->add(new People());
        }

        $editForm = $this->createForm(new EquipmentType($this->getUser()), $equipment);

        return $this->render('CSISEamBundle:Equipment:edit.html.twig', array(
                    'equipment' => $equipment,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Displays a widget for tags edition for an Equipment
     *  
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipement The Equipment to edit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editTagsAction( Equipment $equipment )
    {
        return $this->render('CSISEamBundle:Equipment:editTags.html.twig', array(
                    'equipment' => $equipment,
        ));
    }

    /**
     * Receives the POST data in order to edit an Equipment 
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \CSIS\EamBundle\Entity\Equipment $equipment The Equipment to edit
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\ResdirectResponse
     */
    public function updateAction( Request $request, Equipment $equipment )
    {
        // Base de données
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new EquipmentType($this->getUser()), $equipment);

        if ( $request->isMethod('POST') ) {
            $editForm->bind($request);

            if ( $editForm->isValid() ) {
                $em->persist($equipment);
                $em->flush();

                $this->addFlash(
                        'valid', sprintf('La mise à jour de votre équipement %s est bien prise en compte.', $equipment->getDesignation())
                );

                return $this->redirect($this->generateUrl('equipment_show', array( 'id' => $equipment->getId() )));
            }
        }

        return $this->render('CSISEamBundle:Equipment:edit.html.twig', array(
                    'equipment' => $equipment,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Displays to the user a message in order to confirm the removal of the Equipment entity
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipment The Equipment to remove
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipment The Equipment to remove
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction( Equipment $entity )
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
     * Displays the list of Equipment's owners
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function credentialsAction( Equipment $equipment )
    {
        return $this->render('CSISEamBundle:Equipment:credentials.html.twig', array(
                    'equipment' => $equipment,
                    'owners' => $equipment->getOwners(),
        ));
    }

    /**
     * Removes an owner from an equipment
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \CSIS\EamBundle\Entity\Equipment $equipment
     * @param \CSIS\UserBundle\Entity\User $owner
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function credentialsRemoveAction( Equipment $equipment, User $owner )
    {
        $em = $this->getDoctrine()->getEntityManager();

        if ( $equipment->getOwners()->contains($owner) ) {
            $equipment->getOwners()->removeElement($owner);
            $this->addFlash(
                    'main_valid', sprintf('Propriétaire %s supprimé.', $owner)
            );
        } else {
            $this->addFlash(
                    'main_error', sprintf('Erreur lors de la supression du propriétaire %s.', $owner)
            );
        }

        $em->flush();

        return $this->redirect($this->generateUrl('equipment_credentials', array( 'id' => $equipment->getId() )));
    }

    /**
     * Adds an owner to an equipment
     * 
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \CSIS\EamBundle\Entity\Equipment $equipment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function credentialsAddAction( Request $request, Equipment $equipment )
    {
        $em = $this->getDoctrine()->getEntityManager();

        $form = $this->createForm(new EquipmentAddOwnerType($this->getUser()), $equipment);

        if ( $request->isMethod('POST') ) {
            $form->bind($request);

            if ( $form->isValid() ) {
                $em->flush();
                return $this->redirect($this->generateUrl('equipment_credentials', array( 'id' => $equipment->getId() )));
            }
        }

        return $this->render('CSISEamBundle:Equipment:addOwner.html.twig', array(
                    'equipment' => $equipment,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Fetch tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     */
    public function fetchTagsAction(Equipment $equipment, Request $request)
    {
        if (!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('The page you are requested is only avalaible by ajax requests');
            
        $equipmentTags = $equipment->getEquipmentTags();

        $json = array('accepted' => array(), 'rejected' => array());

        foreach ($equipmentTags as $equipmentTag)
        {
            if ($equipmentTag->getStatus() == EquipmentTag::ACCEPTED)
            {
                $json['accepted'][] = array('name' => $equipmentTag->getTag()->getTag());
            }
            elseif ($equipmentTag->getStatus() == EquipmentTag::REFUSED)
            {
                $json['rejected'][] = array('name' => $equipmentTag->getTag()->getTag());
            }
        }

        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Fetch relative tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
     */
    public function fetchRelativeTagsAction(Equipment $equipment, Request $request)
    {
        if (!$request->isXmlHttpRequest())
            throw new AccessDeniedHttpException('The page you are requested is only avalaible by ajax requests');
        
        $tags = $equipment->getTags();

        // On attache les pseudos tags
        $this->attachTags($equipment, false);

        // Fetch associated Tags from DB
        $newTags = $this->getDoctrine()->getEntityManager()->getRepository('CSISEamBundle:Tag')->findRelativeTags($equipment->getTags());

        $relativesTag = array();
        foreach ( $newTags as $tag ) {
            if ( !$tags->contains($tag['tag']) ) {
                $relativesTag[] = array('name' => $tag['tag']->getTag());
            }
        }

        $json = array('suggested' => $relativesTag);

        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Update tags for an Equipment
     *
     * @Secure(roles="ROLE_GEST_EQUIP")
     * 
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
                print_r($status); die;
            }

            $em->persist($existingTag);
            $em->persist($equipmentTag);
            $em->persist($equipment);
        }

        $em->flush();

        return new Response(json_encode(array('message' => 'ok')));
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
        $em = $this->getDoctrine()->getEntityManager();
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
