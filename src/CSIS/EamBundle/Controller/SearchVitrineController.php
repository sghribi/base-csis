<?php

namespace CSIS\EamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchVitrineController extends Controller
{
    
    public function indexAction($vitrine, $card, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $tabIdEquipments = $this->get('session')->get('csis_eam_results_search');
        $this->addFlash('notice', $this->get('session')->get('recherche'));

        $equ = array();
        foreach($tabIdEquipments as $idEqu) {
            array_push($equ,$em->getRepository('CSISEamBundle:Equipment')->find($idEqu));
        }

        $equipments = $equ;

            switch($card)
            {
                case "equipments":
                $em->getRepository('CSISEamBundle:Equipment');
                $equipment = $em->find($id);

                $em->getRepository('CSISEamBundle:Laboratory');
                $laboratory = $em->find($equipment.laboratory);

                $em->getRepository('CSISEamBundle:Institution');
                $institution = $em->find($laboratory.institution);

                return $this->render('CSISEamBundle:SuperSearch:layout.html.twig',
                                    array('equipements' =>  $this->splitEqpmtsByLetter($equipments),
                                        'vitrine' => $vitrine,
                                        'card' => $card, 'id' => $id,
                                        'equipment' => $equipment,
                                        'laboratory' => $laboratory,
                                        'institution' => $institution,
                                        'nb' => sizeof($equipments)));
                break;

                default :
                            return $this->render('CSISEamBundle:SuperSearch:layout.html.twig', array('equipements' =>  $this->splitEqpmtsByLetter($equipments), 'vitrine' => $vitrine, 'card' => $card, 'id' => $id, 'nb' => sizeof($equipments)));
                break;
            }
    }

    public function splitEqpmtsByLetter($equipements)
    {
        $eqpmtsByLetter = array();

        foreach ($equipements as $eqpmt) {
            $eqpmtsByLetter[strtoupper(substr($eqpmt->getDesignation(),0,1))][] = $eqpmt;
        }

        if($equipements) {
            return $eqpmtsByLetter;
        }

		return;
    }
}
