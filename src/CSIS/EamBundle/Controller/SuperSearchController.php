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
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * Search controller.
 *
 */
class SuperSearchController extends Controller
{    	
	
	/**
	 * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
	 */
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
					$this->container->get('session')->set('recherche', $this->searchToString($entity));

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

    private function searchToString($superSearch)
    {
		$stringResult = $superSearch->getForm1()->getOpen();
        $stringResult .= " " . $superSearch->getForm1()->getTag();

        if ($superSearch->getForm2()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm2()->getBooleans();
            $stringResult .= " " . $superSearch->getForm2()->getOpen();
            $stringResult .= " " . $superSearch->getForm2()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm2()->getClose();
        }

        if ($superSearch->getForm3()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm3()->getBooleans();
            $stringResult .= " " . $superSearch->getForm3()->getOpen();
            $stringResult .= " " . $superSearch->getForm3()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm3()->getClose();
        }

        if ($superSearch->getForm4()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm4()->getBooleans();
            $stringResult .= " " . $superSearch->getForm4()->getOpen();
            $stringResult .= " " . $superSearch->getForm4()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm4()->getClose();
        }

        if ($superSearch->getForm5()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm5()->getBooleans();
            $stringResult .= " " . $superSearch->getForm5()->getOpen();
            $stringResult .= " " . $superSearch->getForm5()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm5()->getClose();
        }

        if ($superSearch->getForm6()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm6()->getBooleans();
            $stringResult .= " " . $superSearch->getForm6()->getOpen();
            $stringResult .= " " . $superSearch->getForm6()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm6()->getClose();
        }

        if ($superSearch->getForm7()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm7()->getBooleans();
            $stringResult .= " " . $superSearch->getForm7()->getOpen();
            $stringResult .= " " . $superSearch->getForm7()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm7()->getClose();
        }

        if ($superSearch->getForm8()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm8()->getBooleans();
            $stringResult .= " " . $superSearch->getForm8()->getOpen();
            $stringResult .= " " . $superSearch->getForm8()->getTag() . " ";
            $stringResult .= " " . $superSearch->getForm8()->getClose();
        }

        return $stringResult;

    }
	
}
