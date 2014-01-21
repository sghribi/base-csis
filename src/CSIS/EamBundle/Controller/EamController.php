<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EamController extends Controller {

  public function indexAction() {
    return $this->render('CSISEamBundle:Eam:index.html.twig', array());
  }

}
