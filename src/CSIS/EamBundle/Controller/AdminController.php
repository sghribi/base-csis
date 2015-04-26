<?php

namespace CSIS\EamBundle\Controller;

use Doctrine\ORM\EntityManager;
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
    public function indexAction()
    {
        /** @var EntityManager $em **/
        $em = $this->getDoctrine()->getManager();
        $waitingUsers  = $em->getRepository('CSISUserBundle:User')->waitingUsers();

        return array(
            'waiting_users'     => $waitingUsers,
        );
    }

}
