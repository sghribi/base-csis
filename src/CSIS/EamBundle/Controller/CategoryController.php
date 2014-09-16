<?php

namespace CSIS\EamBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\EamBundle\Entity\Category;
use CSIS\EamBundle\Form\CategoryType;

/**
 * @Route("/categories")
 *
 * Category controller.
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Category:index.html.twig")
     * @Route("/{page}", name="category", requirements={"page" = "\d+"}, defaults={"page" = 1})
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $maxPerPage = $this->container->getParameter('csis_admin_views_max_in_lists');
        $categories = $em->getRepository('CSISEamBundle:Category')->findByOrderNamePaginated($page, $maxPerPage);;

        return array(
            'categories' => $categories,
            'page' => $page,
            'nbPages' => ceil(count($categories) / $maxPerPage),
        );
    }

    /**
     * Finds and displays a Category entity.
     * @Secure(roles="ROLE_GEST_EQUIP")
     * @Template("CSISEamBundle:Category:show.html.twig")
     * @Route("/{id}/show", name="category_show", requirements={"id" = "\d+"})
     */
    public function showAction(Category $category)
    {
        $deleteForm = $this->createDeleteForm($category->getId());

        return array(
            'category'      => $category,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Displays a form to create a new Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     * @Template("CSISEamBundle:Category:new.html.twig")
     * @Route("/new", name="category_new")
     */
    public function newAction()
    {
        $category = new Category();
        $form   = $this->createForm(new CategoryType(), $category);

        return array(
            'category' => $category,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     * @Template("CSISEamBundle:Category:new.html.twig")
     * @Route("/create", name="category_create")
     */
    public function createAction(Request $request)
    {
        $category  = new Category();
        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Famille ajoutée !');
            return $this->redirect($this->generateUrl('category_show', array('id' => $category->getId())));
        }

        return array(
            'category' => $category,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     * @Template("CSISEamBundle:Category:edit.html.twig")
     * @Route("{id}/edit", name="category_edit", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function editAction(Category $category)
    {
        $editForm = $this->createForm(new CategoryType(), $category);
        $deleteForm = $this->createDeleteForm($category->getId());

        return array(
            'category'    => $category,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     * @Route("{id}/update", name="category_update", requirements={"id" = "\d+"})
     * @Method({"POST"})
     * @Template("CSISEamBundle:Category:edit.html.twig")
     */
    public function updateAction(Category $category, Request $request)
    {
        $deleteForm = $this->createDeleteForm($category->getId());
        $editForm = $this->createForm(new CategoryType(), $category);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('valid', 'Famille modifiée !');
            return $this->redirect($this->generateUrl('category_edit', array('id' => $category->getId())));
        }

        return array(
            'category'      => $category,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     * @Secure(roles="ROLE_GEST_CATEGORY")
     * @Route("{id}/delete", name="category_delete")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
