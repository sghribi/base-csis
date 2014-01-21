<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Form\EquipmentType;
use CSIS\EamBundle\Entity\Tag;
use CSIS\EamBundle\Entity\Search;
use CSIS\EamBundle\Entity\SuperSearch;
use CSIS\EamBundle\Form\SearchType;
use CSIS\EamBundle\Form\SuperSearchType;


/**
 * Search controller.
 *
 */
class SuperSearchController extends Controller
{    	
	public function indexAction(Request $request)
    {
		$entity = new SuperSearch();			
		$form = $this->createForm(new SuperSearchType(), $entity);

		$error = null;
	 
		if ($request->getMethod() == 'POST') {
		  $form->bind($request);
		  //if ($form->isValid()) {			  
		  
			$pattern = '/^\({0,}$/';
			$nbOpen = 0;
			$nbClose = 0;
			
			$subject = $entity->getForm1()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm2()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);	
				
			$subject = $entity->getForm3()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm4()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm5()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm6()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm7()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$subject = $entity->getForm8()->getOpen();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbOpen +=  strlen($subject);
				
			$pattern = '/^\){0,}$/';

			$subject = $entity->getForm1()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm2()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm3()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm4()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm5()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm6()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm7()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";
			else
				$nbClose +=  strlen($subject);
				
			$subject = $entity->getForm8()->getClose();
			if ( !preg_match($pattern, $subject) ) 
				$error = "Les champs 'parenthèse' ne doivent contenir que des parenthèses.";			
		    else
				$nbClose +=  strlen($subject);
			
			
		  if($error == null) {
			if($nbOpen < $nbClose) {
				$manque = $nbClose - $nbOpen;
				$error = "Il manque ".$manque." parenthèse(s) ouvrante(s).";
			} else if($nbOpen > $nbClose) {
				$manque = $nbOpen - $nbClose;
				$error = "Il manque ".$manque." parenthèse(s) fermante(s).";
			}
		  }
			
			if($error == null) {
				$em = $this->getDoctrine()->getManager();
				$repo = $em->getRepository('CSISEamBundle:Equipment');
				$results = $repo->findSearchedEquipments($entity);
				
				if($results)
				{
					$tabIdEquipments = array();
					
					foreach ($results as $e)
					{
						array_push($tabIdEquipments,$e->getId());
					}
					
					$this->container->get('session')->set('csis_eam_results_search', $tabIdEquipments);

					return $this->redirect($this->generateUrl('supersearch_test', array(	'vitrine' => 'alphabetique', 
																			'card' => 'vide', 
																			'id' => 0,
																			)));
				}
				
				else
				{
					$error = "Aucun résultat trouvé";
				//*
				  return $this->render('CSISEamBundle:SuperSearch:index.html.twig', array(
				  'form' => $form->createView(),
				  'error' => $error,
				  'results' => '',
				));
				//*/	
				}
			}
			 
		}	
				
		return $this->render('CSISEamBundle:SuperSearch:index.html.twig', array(
          'form' 	=> $form->createView(),
		  'error' 	=> $error,
		  'results' => '',
        ));       
    }
	
}
