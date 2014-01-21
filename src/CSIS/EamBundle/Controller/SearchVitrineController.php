<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Form\EquipmentType;

class SearchVitrineController extends Controller
{
    
    public function indexAction($vitrine, $card, $id)
    {
        $em = $this->getDoctrine()->getManager();
		
		$tabIdEquipments = $this->container->get('session')->get('csis_eam_results_search');
		$equ = array();
		foreach($tabIdEquipments as $idEqu)
		{
			array_push($equ,$em->getRepository('CSISEamBundle:Equipment')->find($idEqu));
		}
		
		$equipments = $equ; //$em->getRepository('CSISEamBundle:Equipment')->findAll();			
			
			switch($card)
			{
				case "equipments":
				$em->getRepository('CSISEamBundle:Equipment');
				$equipment = $em->find($id);
				
				$em->getRepository('CSISEamBundle:Category');
				$categories = $em->find($equipment.categories);
				
				$em->getRepository('CSISEamBundle:Laboratory');
				$laboratory = $em->find($equipment.laboratory);
				
				$em->getRepository('CSISEamBundle:Institution');
				$institution = $em->find($laboratory.institution);
				
				return $this->render('CSISEamBundle:SuperSearch:layout.html.twig', 
									array('equipements' =>  $this->splitEqpmtsByLetter($equipments), 
										'vitrine' => $vitrine, 
										'card' => $card, 'id' => $id, 
										'equipment' => $equipment, 
										'categories' => $categories, 
										'laboratory' => $laboratory, 
										'institution' => $institution,));   
				break;			
			
				default : 
							return $this->render('CSISEamBundle:SuperSearch:layout.html.twig', array('equipements' =>  $this->splitEqpmtsByLetter($equipments), 'vitrine' => $vitrine, 'card' => $card, 'id' => $id,));
				break;
			}
    }
    
    public function splitEqpmtsByLetter($equipements)
    {
        foreach ($equipements as $eqpmt)
        {
            $eqpmtsByLetter[strtoupper(substr($eqpmt->getDesignation(),0,1))][] = $eqpmt;
        }
        
        if($equipements) return $eqpmtsByLetter;
		else return;
    }
}
?>
