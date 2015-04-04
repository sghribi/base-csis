<?php

namespace CSIS\EamBundle\Controller;

use CSIS\EamBundle\Entity\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class VitrineController extends Controller
{
    public function indexAction($vitrine, $card, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch($vitrine) {
            case "alphabetique":
                /** @var EquipmentRepository $repository */
                $repository = $em->getRepository('CSISEamBundle:Equipment');
                $equipments = $repository->findAllOrderByDesignation();

                switch($card) {
                    case "equipment":
                        $repository = $em->getRepository('CSISEamBundle:Equipment');
                        $equipment = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'equipements' =>  $this->splitEqpmtsByLetter($equipments),
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'equipment' => $equipment,
                        ));
                    break;

                    case "laboratory":
                        $repository = $em->getRepository('CSISEamBundle:Laboratory');
                        $laboratory = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'equipements' =>  $this->splitEqpmtsByLetter($equipments),
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'laboratory' => $laboratory,
                            ));
                    break;

                    case "institution":
                        $repository = $em->getRepository('CSISEamBundle:Institution');
                        $institution = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'equipements' =>  $this->splitEqpmtsByLetter($equipments),
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'institution' => $institution,
                        ));
                    break;

                    default :
                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'equipements' =>  $this->splitEqpmtsByLetter($equipments),
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                        ));
                    break;
                }

            break;

            case "laboratories" :
                $repository = $em->getRepository('CSISEamBundle:Institution');
                $institutions = $repository->findAll();

                switch($card) {
                    case "equipment":
                        $repository = $em->getRepository('CSISEamBundle:Equipment');
                        $equipment = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'institutions' => $institutions,
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'equipment' => $equipment,
                        ));
                    break;

                    case "laboratory":
                        $repository = $em->getRepository('CSISEamBundle:Laboratory');
                        $laboratory = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'institutions' => $institutions,
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'laboratory' => $laboratory,
                        ));
                    break;

                    case "institution":
                        $repository = $em->getRepository('CSISEamBundle:Institution');
                        $institution = $repository->find($id);

                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'institutions' => $institutions,
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                            'institution' => $institution,
                        ));
                    break;

                    default :
                        return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                            'institutions' => $institutions,
                            'vitrine' => $vitrine,
                            'card' => $card,
                            'id' => $id,
                        ));
                    break;
                }

            break;

            default :
                /** @var EquipmentRepository $repository */
                $repository = $em->getRepository('CSISEamBundle:Equipment');
                $equipments = $repository->findAllOrderByDesignation();

                return $this->render('CSISEamBundle:Vitrine:layout.html.twig', array(
                    'equipements' =>  $this->splitEqpmtsByLetter($equipments),
                    'vitrine' => $vitrine,
                    'card' => $card,
                    'id' => $id,
                ));
            break;
        }
    }

    public function cardAction($card, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch($card) {
            case "equipment":
                $repository = $em->getRepository('CSISEamBundle:Equipment');
                $equipment = $repository->find($id);

                return $this->render('CSISEamBundle:Vitrine:card_equipment.html.twig', array('equipment' => $equipment,));
            break;

            case "laboratory":
                $repository = $em->getRepository('CSISEamBundle:Laboratory');
                $laboratory = $repository->find($id);

                return $this->render('CSISEamBundle:Vitrine:card_laboratory.html.twig', array('laboratory' => $laboratory,));
            break;

            case "institution":
                $repository = $em->getRepository('CSISEamBundle:Institution');
                $institution = $repository->find($id);

                return $this->render('CSISEamBundle:Vitrine:card_institution.html.twig', array('institution' => $institution,));
            break;

            default :
                return $this->render('CSISEamBundle:Vitrine:card_vide.html.twig', array());
            break;
        }
    }

    public function splitEqpmtsByLetter($equipements)
    {
        $eqpmtsByLetter = array();

        foreach ($equipements as $eqpmt) {
            $eqpmtsByLetter[strtoupper(substr($this->deleteAccent($eqpmt->getDesignation()),0,1))][] = $eqpmt;
        }

        return $eqpmtsByLetter;
    }

    public function deleteAccent($word)
    {
        $word = mb_strtolower($word, 'UTF-8');
        $word = str_replace(array(  'à', 'â', 'ä', 'á', 'ã', 'å',
                                    'î', 'ï', 'ì', 'í',
                                    'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
                                    'ù', 'û', 'ü', 'ú',
                                    'é', 'è', 'ê', 'ë',
                                    'ç', 'ÿ', 'ñ',
                            ),
                            array(  'a', 'a', 'a', 'a', 'a', 'a',
                                    'i', 'i', 'i', 'i',
                                    'o', 'o', 'o', 'o', 'o', 'o',
                                    'u', 'u', 'u', 'u',
                                    'e', 'e', 'e', 'e',
                                    'c', 'y', 'n',
                            ),
                            $word
                );

        return $word;
    }
}
