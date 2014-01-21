<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Category;
use CSIS\EamBundle\Form\CategoryType;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');

        $categories = $em->getRepository('CSISEamBundle:Category')->findByOrderNamePaginated($page, $maxPerPage);;

        return $this->render('CSISEamBundle:Category:index.html.twig', array(
            'categories' => $categories,
            'page' => $page,
            'nbPages' => ceil(count($categories) / $maxPerPage),
        ));
    }

    /**
     * Finds and displays a Category entity.
     * @Secure(roles="ROLE_GEST_EQUIP")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CSISEamBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:Category:show.html.twig', array(
            'category'      => $category,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     */
    public function newAction()
    {
        $category = new Category();
        $form   = $this->createForm(new CategoryType(), $category);

        return $this->render('CSISEamBundle:Category:new.html.twig', array(
            'category' => $category,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     */
    public function createAction(Request $request)
    {
        $category  = new Category();
        $form = $this->createForm(new CategoryType(), $category);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Famille ajoutée !');
            return $this->redirect($this->generateUrl('category_show', array('id' => $category->getId())));
        }

        return $this->render('CSISEamBundle:Category:new.html.twig', array(
            'category' => $category,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CSISEamBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createForm(new CategoryType(), $category);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CSISEamBundle:Category:edit.html.twig', array(
            'category'      => $category,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CSISEamBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoryType(), $category);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Famille modifiée !');
            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return $this->render('CSISEamBundle:Category:edit.html.twig', array(
            'category'      => $category,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('CSISEamBundle:Category')->find($id);

            if (!$category) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($category);
            $em->flush();
            $this->get('session')->getFlashBag()->add('valid', 'Famille supprimée !');
        }

        return $this->redirect($this->generateUrl('category'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
