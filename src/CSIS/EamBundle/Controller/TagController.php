<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Tag;
use CSIS\EamBundle\Form\TagType;

/**
 * Tag controller.
 */
class TagController extends Controller
{
    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function indexAction($onglet, $page)
    {
        // Tag form creation
        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag);

        // Useful vars
        $page_all = 1; // Numéro de page pour tous les tags
        $page_att = 1; // Numéro de page pour les en attentes
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists'); // Nombre de entités par page

        // Handle tabs
        if ($onglet == "edit") {
            $page_att = $page;
        } else {
            $page_all = $page;
        }

        /** Listes des tags **/
        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');

        // Liste de tous les tags
        $tags_all = $tagRepository->findTagsWithNumberOfUse($page_all, $maxPerPage);
        // Liste des tags ayant comme status en attente
        $tags_att = $tagRepository->findTagsStandByWithNumberOfUse($page_att, $maxPerPage);

        $nbPages_all = ceil(count($tagRepository->findAll()) / $maxPerPage);
        $nbPages_att = ceil(count($tagRepository->findBy(array( 'status' => 0 ))) / $maxPerPage);

        // Affiche la page
        return $this->render('CSISEamBundle:Tag:index.html.twig', array(
            'form' => $form->createView(),
            'tags_all' => $tags_all,
            'tags_att' => $tags_att,
            'page_all' => $page_all,
            'page_att' => $page_att,
            'nbPages_all' => $nbPages_all,
            'nbPages_att' => $nbPages_att,
            'onglet' => $onglet,
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function createAction( Request $request )
    {
        $em = $this->getDoctrine()->getManager();
        $tagRepository = $em->getRepository('CSISEamBundle:Tag');

        // On retourne à la page principale avec les valeurs saisies non validées
        $maxPerPage = $this->container->getParameter('csis_admin_index_max_in_lists');
        $entities = $tagRepository->findTagsWithNumberOfUse(1, $maxPerPage); // Liste de tous les tags
        $entities_s = $tagRepository->findTagsStandByWithNumberOfUse(1, $maxPerPage); // Liste des tags ayant comme status en attente
        $nbPages = ceil(count($tagRepository->findAll()) / $maxPerPage);
        $nbPages_s = ceil(count($tagRepository->findBy(array( 'status' => Tag::PENDING ))) / $maxPerPage);

        $entity = new Tag();
        $form = $this->createForm(new TagType(), $entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('session')->getFlashBag()->add('valid', 'Tag <strong>' . $entity->getTag() . '</strong> ajouté !');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tag'));
        }

        // Affiche la page
        return $this->render('CSISEamBundle:Tag:index.html.twig', array(
                    'form' => $form->createView(),
                    'tags_all' => $entities,
                    'tags_att' => $entities_s,
                    'page_all' => 1,
                    'page_att' => 1,
                    'nbPages_all' => $nbPages,
                    'nbPages_att' => $nbPages_s,
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function editAction( Tag $entity )
    {
        $em = $this->getDoctrine()->getManager();
        $tagRepository = $em->getRepository('CSISEamBundle:Tag');

        // Formulaire de modification
        $editForm = $this->createForm(new TagType(), $entity);

        // On retourne à la page principale avec les valeurs saisies non validées
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $entities = $tagRepository->findTagsWithNumberOfUse(1, $maxPerPage); // Liste de tous les tags
        $entities_s = $tagRepository->findTagsStandByWithNumberOfUse(1, $maxPerPage); // Liste des tags ayant comme status en attente
        $nbPages = ceil(count($tagRepository->findAll()) / $maxPerPage);
        $nbPages_s = ceil(count($tagRepository->findBy(array( 'status' => Tag::PENDING ))) / $maxPerPage);

        return $this->render('CSISEamBundle:Tag:edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'tags_all' => $entities,
            'tags_att' => $entities_s,
            'tag' => $entity,
            'page_all' => 1,
            'page_att' => 1,
            'nbPages_all' => $nbPages,
            'nbPages_att' => $nbPages_s,
            'onglet' => 'list',
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function updateAction( Request $request, Tag $entity )
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CSISEamBundle:Tag');

        // On récupère les informations originales
        $tag = $entity->getTag();
        $status = $entity->getStatus();

        // Reconstruction du formulaire de modification
        $editForm = $this->createForm(new TagType(), $entity);

        // On récupère les données modifiées copié sur le formulaire + copié sur l'entité
        $editForm->handleRequest($request);

        // On vérifie si les valeurs saisies sont valides
        if ( $editForm->isValid() ) {
            // On vérifie si il y a doublons avant mise à jour
            $exist = $repo->findOneBy(array( 'tag' => $entity->getTag() ));
            if ( $exist != null && $exist->getId() != $entity->getId() ) {
                // Si il y a doublons
                $this->get('session')->getFlashBag()->add('error', 'Le tag <strong>' . $entity->getTag() . '</strong> existe déjà.');
                // On n'écrase pas les informations de l'entité à cause du bind même si il n'y a pas de validation "flush"
                $entity->setTag($tag);
                $entity->setStatus($status);
            } else {
                $em->persist($entity); // Mise à jour
                $em->flush(); // Validation mise à jour
                $this->get('session')->getFlashBag()->add('valid', 'La mise à jour du tag <strong>' . $entity->getTag() . '</strong> est bien prise en compte.');

                // Redirection vers la page principale
                return $this->redirect($this->generateUrl('tag'));
            }
        }

        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $entities = $repo->findTagsWithNumberOfUse(1, $maxPerPage); // Liste de tous les tags
        $entities_s = $repo->findTagsStandByWithNumberOfUse(1, $maxPerPage); // Liste des tags ayant comme status en attente
        $nbPages = ceil(count($repo->findAll()) / $maxPerPage);
        $nbPages_s = ceil(count($repo->findBy(array( 'status' => 0 ))) / $maxPerPage);
        return $this->render('CSISEamBundle:Tag:edit.html.twig', array(
                    'edit_form' => $editForm->createView(),
                    'tags_all' => $entities,
                    'tags_att' => $entities_s,
                    'tag' => $entity,
                    'page_all' => 1,
                    'page_att' => 1,
                    'nbPages_all' => $nbPages,
                    'nbPages_att' => $nbPages_s,
                    'onglet' => 'list',
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function askDeleteAction(Tag $tag)
    {
        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');

        // On vérifie si le tag existe
        $exist = $tagRepository->isTagUsed($tag);
        if ($exist) {
            $this->addFlash('main_error', 'Suppression impossible : le tag <strong>' . $tag->getTag() . '</strong>, est utilisé dans les équipements.');
        } else {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer le tag <strong>' . $tag->getTag() . '</strong> ?';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('tag_delete', array( 'id' => $tag->getId() )) . '">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('tag') . '">Non</a>';
            $this->addFlash('main_valid', $message);
        }

        // Redirection vers la page principale
        return $this->redirectToRoute('tag');
    }

    public function autocompleteAction( Request $request )
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('La page à laquelle vous tentez d\'accéder ne fonctionne que par ajax');
        }

        $tagRepo = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');
        $input = $request->request->get('input');
        $tags = $tagRepo->findAutocomplete($input);
        $data = array();
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $data[] = $tag->getTag();
            }
        } else {
            $data[] = 'Aucun résulat trouvé.';
        }

        return new JsonResponse($data);
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function deleteAction(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($tag);
        $em->flush();

        $this->addFlash('main_valid', 'Tag <strong>' . $tag->getTag() . '</strong>, supprimé !');

        return $this->redirectToRoute('tag');
    }
}
