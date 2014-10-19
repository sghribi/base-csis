<?php

namespace CSIS\UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CSIS\UserBundle\Entity\User;
use CSIS\UserBundle\Form\AddLabType;
use CSIS\UserBundle\Form\AddInstitutionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    
    /**
     * Find and displays all users beneath me
     * @Secure(roles="ROLE_GEST_USER")
     * @Template("CSISUserBundle:Admin:index.html.twig")
     */
    public function indexAction($page)
    {
        $user = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CSISUserBundle:User');
        $maxPerPage = $this->container->getParameter('csis_user_max_per_page');
        
        $users = $userRepo->findAllOrderByNameByEnabledPaginated($page, $maxPerPage);

        return array(
            'users' => $users,
            'page' => $page,
            'nbPages'  => ceil( count($users) / $maxPerPage ),
        );
    }

    /**
     * Finds and displays a User.
     * @Secure(roles="ROLE_GEST_USER")
     * @Template("CSISUserBundle:Admin:show.html.twig")
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User.
     * @Secure(roles="ROLE_GEST_USER")
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm($this->container->get('csis.user.form'), $user, array('password' => $user->isEnabled()));
        
        if ( $request->isMethod('POST') ) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {

                //
                if (!$user->isEnabled()) {
                    $user->setEnabled(true);

                }

                $this->get('fos_user.user_manager')->updateUser($user);
                
                $route = 'csis_user_show';
                // If the user isn't admin, the user must have a dependancy for an institution or a lab
                if ( !$user->hasRole('ROLE_ADMIN') ) {
                    if ( $user->hasRole('ROLE_GEST_ESTAB')) {
                        // do nothing
                    } else if ( $user->hasRole('ROLE_GEST_LAB') ) {
                        $route = 'csis_user_edit_institution';
                    } else {
                        $route = 'csis_user_edit_lab';
                    }
                }

                return $this->redirect($this->generateUrl($route, array('id' => $user->getId())));
            }
        }
        return $this->render('CSISUserBundle:Admin:edit.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView(),
        ));
        
    }

    /**
     * Deletes a User.
     * @Secure(roles="ROLE_GEST_USER")
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    public function deleteAction(Request $request, User $user) {
        $form = $this->createDeleteForm($user);
        
        if ( $request->isMethod('POST') ) {
            $form->handleRequest($request);
            
            $data = $form->getData();
            if ($data['id'] == $user->getId()) {
                $em = $this->getDoctrine()->getManager();

                $em->remove($user);
                $em->flush();
            } else {
                throw $this->createNotFoundException();
            }
            
        }

        return $this->redirect($this->generateUrl('csis_user_index'));
    }
    
    /**
     * Edits a User.
     * @Secure(roles="ROLE_GEST_USER")
     * @Template("CSISUserBundle:Admin:editLab.html.twig")
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    public function editLaboratoryDependanceAction(Request $request, User $user) {
        $form = $this->createForm(
            new AddLabType($this->getUser()),
            $user
        );
        
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                $userManager = $this->container->get('fos_user.user_manager');
                
                $user->setInstitution(null);
                $userManager->updateUser($user);

                return $this->redirect($this->generateUrl('csis_user_show', array('id' => $user->getId())));
            }
        }
        
        return array(
            'form' => $form->createView(),
            'user' => $user,
        );
    }
    
    /**
     * Edits a User.
     * @Secure(roles="ROLE_GEST_USER")
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    public function editInstitutionDependanceAction(Request $request, User $user) {
        $form = $this->createForm(
            new AddInstitutionType($this->container->get('security.context')->getToken()->getUser()),
            $user
        );
        
        if ( $request->isMethod('POST') ) {
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                $userManager = $this->container->get('fos_user.user_manager');
                
                $user->setLab(null);
                $userManager->updateUser($user);

                return $this->redirect($this->generateUrl('csis_user_show', array('id' => $user->getId())));
            }
        }
        
        return $this->render('CSISUserBundle:Admin:editInstitution.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    /**
     * Creates a form to delete a User by id.
     * @param mixed $id The entity id
     * @return \Symfony\Component\Form\Form The form
     * @ParamConverter("user", class="CSISUserBundle:User")
     */
    private function createDeleteForm(User $user) {
        return $this->createFormBuilder(array('id' => $user->getId()))
                    ->add('id', 'hidden')
                    ->getForm()
        ;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws AccessDeniedHttpException
     * @Secure(roles="ROLE_GEST_USER")
     */
    public function autocompleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CSISUserBundle:User');
        $input = $request->request->get('input');

        if ($request->isXmlHttpRequest() ) {
            $users = $userRepo->findAutocomplete($input);
            $data = array();
            if (count($users) > 0) {
                /** @var User $user */
                foreach ($users as $user) {
                    $data[] = $user->getEmail();
                }
            }
            return new Response(json_encode($data));

        } else {
            throw new AccessDeniedHttpException(
                'La page à laquelle vous tentez d\'accéder ne fonctionne que par ajax'
            );
        }
    }
}
