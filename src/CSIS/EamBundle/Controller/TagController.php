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
    public function indexAction($onglet)
    {
        // Tag form creation
        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag);

        /** Listes des tags **/
        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');
        $tags_all = $tagRepository->findTagsWithNumberOfUse();

        // Affiche la page
        return $this->render('CSISEamBundle:Tag:index.html.twig', array(
            'form' => $form->createView(),
            'tags_all' => $tags_all,
            'onglet' => $onglet,
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function createAction( Request $request )
    {
        // Fetch all tags
        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');
        $tags_all = $tagRepository->findTagsWithNumberOfUse();

        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag->setStatus(Tag::ACCEPTED);

            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('valid', 'Tag <strong>' . $tag->getTag() . '</strong> ajouté !');
            return $this->redirectToRoute('tag');
        }

        return $this->render('CSISEamBundle:Tag:index.html.twig', array(
            'form' => $form->createView(),
            'tags_all' => $tags_all,
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function editAction( Tag $tag )
    {
        // Fetch all tags
        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');
        $tags = $tagRepository->findTagsWithNumberOfUse();

        $editForm = $this->createForm(new TagType(), $tag);

        return $this->render('CSISEamBundle:Tag:edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'tags_all' => $tags,
            'tag' => $tag,
        ));
    }

    /**
     * @Secure(roles="ROLE_GEST_TAGS")
     */
    public function updateAction(Request $request, Tag $tag)
    {
        $editForm = $this->createForm(new TagType(), $tag);
        $editForm->handleRequest($request);

        if ( $editForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash('valid', 'La mise à jour du tag <strong>' . $tag->getTag() . '</strong> est bien prise en compte.');

            return $this->redirectToRoute('tag');
        }

        $tagRepository = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');
        $tags = $tagRepository->findTagsWithNumberOfUse();

        return $this->render('CSISEamBundle:Tag:edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'tags_all' => $tags,
            'tag' => $tag,
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

        return $this->redirectToRoute('tag');
    }

    /**
     *
     */
    public function autocompleteAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedHttpException('La page à laquelle vous tentez d\'accéder ne fonctionne que par ajax');
        }

        $tagRepo = $this->getDoctrine()->getRepository('CSISEamBundle:Tag');

        $input = $request->request->get('input');
        $tags = $tagRepo->findAutocomplete($input);

        $data = array();
        if (count($tags) > 0) {
            /** @var Tag $tag */
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
