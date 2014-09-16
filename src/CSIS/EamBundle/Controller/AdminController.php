<?php

namespace CSIS\EamBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Admin controller.
 * @Route("/admin")
 */
class AdminController extends Controller {

    /**
     * Homepage of admin space
     * @Secure(roles="ROLE_USER")
     * @Template("CSISEamBundle:Admin:index.html.twig")
     * @Route("/", name="csis_eam_admin_index")
     */
    public function indexAction() {
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $max = $this->container->getParameter('csis_admin_index_max_in_lists');
        
        $equipments   = $em->getRepository('CSISEamBundle:Equipment')->findByOwnersOrderByEditDatePaginated($user, 1, $max);
        $labs         = $em->getRepository('CSISEamBundle:Laboratory')->findByOwnersOrderByEditDatePaginated($user, 1, $max);
        $institutions = $em->getRepository('CSISEamBundle:Institution')->findByOwnersOrderByEditDatePaginated($user, 1, $max);
        $peoples      = $em->getRepository('CSISEamBundle:People')->findByOrderNamePaginated(1, $max);
        $categories   = $em->getRepository('CSISEamBundle:Category')->findByOrderNamePaginated(1, $max);
        $waitingTags  = $em->getRepository('CSISEamBundle:Tag')->waitingTags();

        return array(
            'peoples'          => $peoples,
            'peoples_sup'      => ( count($peoples) > $max ),
            'categories'       => $categories,
            'categories_sup'   => ( count($categories) > $max ),
            'equipments'       => $equipments,
            'equipments_sup'   => ( count($equipments) > $max ),
            'labs'             => $labs,
            'labs_sup'         => ( count($labs) > $max ),
            'institutions'     => $institutions,
            'institutions_sup' => ( count($institutions) > $max ),
            'waiting_tags'     => $waitingTags
        );
    }

}
