<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Tag;
use CSIS\EamBundle\Form\TagType;

/**
 * Tag controller.
 *
 */
class TagController extends Controller
{

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function indexAction( $onglet, $page )
    {
        // Formulaire de tag
        $entity = new Tag();
        $form = $this->createForm(new TagType(), $entity);

        // Liste des tags
        $em = $this->getDoctrine()->getManager(); // Récupère la base
        $repo = $em->getRepository('CSISEamBundle:Tag');

        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists'); // Nombre de entités par page
        $page_all = 1; // Numéro de page pour tous les tags
        $page_att = 1; // Numéro de page pour les en attentes
        if ( $onglet == "edit" ) {
            $page_att = $page;
        } else {
            $page_all = $page;
        }
        $tags_all = $repo->findTagsWithNumberOfUse($page_all, $maxPerPage); // Liste de tous les tags
        $tags_att = $repo->findTagsStandByWithNumberOfUse($page_att, $maxPerPage); // Liste des tags ayant comme status en attente
        $nbPages_all = ceil(count($repo->findAll()) / $maxPerPage);
        $nbPages_att = ceil(count($repo->findBy(array( 'status' => 0 ))) / $maxPerPage);

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
        $repo = $em->getRepository('CSISEamBundle:Tag');

        // Création du formulaire de création de tag
        $entity = new Tag();
        $form = $this->createForm(new TagType(), $entity);

        // On récupère les valeurs saisies sur le formulaire
        $form->bind($request);

        // On vérifie si les champs sont valides
        if ( $form->isValid() ) {
            // On vérifie si on ajoute pas deux fois le même tag
            $exist = $repo->findOneBy(array( 'tag' => $entity->getTag() ));

            if ( $exist == null ) {
                // Si il n'existe pas
                $this->get('session')->getFlashBag()->add('valid', 'Tag <strong>' . $entity->getTag() . '</strong> ajouté !');
                $em->persist($entity);
                $em->flush();

                // Redirection vers la page principal avec les champs vidés
                return $this->redirect($this->generateUrl('tag'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Le tag  <strong>' . $entity->getTag() . '</strong> existe déjà.');
            }
        }

        // On retourne à la page principale avec les valeurs saisies non validées
        $maxPerPage = $this->container->getParameter('csis_admin_index_max_in_lists');
        $entities = $repo->findTagsWithNumberOfUse(1, $maxPerPage); // Liste de tous les tags
        $entities_s = $repo->findTagsStandByWithNumberOfUse(1, $maxPerPage); // Liste des tags ayant comme status en attente
        $nbPages = ceil(count($repo->findAll()) / $maxPerPage);
        $nbPages_s = ceil(count($repo->findBy(array( 'status' => 0 ))) / $maxPerPage);
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
        $repo = $em->getRepository('CSISEamBundle:Tag');

        // Formulaire de modification
        $editForm = $this->createForm(new TagType(), $entity);

        // On retourne à la page principale avec les valeurs saisies non validées
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
        $editForm->bind($request);

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
    public function askDeleteAction( Tag $entity )
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('CSISEamBundle:Tag');

        // On vérifie si le tag existe
        $exist = $repo->isTagUsed($entity->getId());
        if ( $exist == true ) {
            $this->get('session')->getFlashBag()->add('main_error', 'Suppression impossible : le tag <strong>' . $entity->getTag() . '</strong>, est utilisé dans les équipements.');
        } else {
            // Message de confirmation
            $message = 'Etes-vous sûr de bien vouloir supprimer le tag <strong>' . $entity->getTag() . '</strong> ?';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('tag_delete', array( 'id' => $entity->getId() )) . '">Oui</a>';
            $message .= '&nbsp;&nbsp<a href="' . $this->generateUrl('tag') . '">Non</a>';
            $this->get('session')->getFlashBag()->add('main_valid', $message);
        }
        // Redirection vers la page principale
        return $this->redirect($this->generateUrl('tag'));
    }
    
    public function autocompleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $tagRepo = $em->getRepository('CSISEamBundle:Tag');
        $input = $request->request->get('input');

        if ( $this->getRequest()->isXmlHttpRequest() ) {
            $tags = $tagRepo->findAutocomplete($input);

            $data = array( );
            if ( count($tags) > 0 ) {
                foreach ( $tags as $tag )
                    $data[] = $tag->getTag();
            } else {
                $data[] = 'Aucun résulat trouvé.';
            }

            return new Response(json_encode($data));
        } 
        
        throw new AccessDeniedHttpException('La page à laquelle vous tentez d\'accéder ne fonctionne que par ajax');
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function deleteAction( Tag $entity )
    {
        $em = $this->getDoctrine()->getManager();
        //$repo = $em->getRepository('CSISEamBundle:Tag');

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('main_valid', 'Tag <strong>' . $entity->getTag() . '</strong>, supprimé !');

        return $this->redirect($this->generateUrl('tag'));
    }

}
