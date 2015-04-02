<?php
/**
 * Created by PhpStorm.
 * User: samy
 * Date: 02/04/15
 * Time: 16:44
 */

namespace CSIS\EamBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Search controller.
 * @Route("/search")
 */
class SearchController extends Controller
{
    const SEARCH_EQUIPMENTS_NAME_RESULTS = 5;
    const SEARCH_EQUIPMENTS_TAGS_RESULTS = 10;

    /**
     * @Route("/search/by-name", name="equipment_by_name_quick_search", defaults={"_format" = "json"}, options={"expose": true})
     * @Method({"GET"})
     * @Rest\View()
     */
    public function quickEquipmentSearchByNameAction (Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $term = $request->query->get('query');

        if ($term == '') {
            return new JsonResponse(array('error' => 'Invalid search.'), Response::HTTP_BAD_REQUEST);
        }

        $equipments = $em->getRepository('CSISEamBundle:Equipment')->searchByName($term, self::SEARCH_EQUIPMENTS_NAME_RESULTS);

        return $equipments;
    }

    /**
     * @Route("/search/by-tags", name="equipment_by_tags_quick_search", defaults={"_format" = "json"}, options={"expose": true})
     * @Method({"GET"})
     * @Rest\View()
     */
    public function quickEquipmentSearchByTagsAction (Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $term = $request->query->get('query');

        if ($term == '') {
            return new JsonResponse(array('error' => 'Invalid search.'), Response::HTTP_BAD_REQUEST);
        }

        $equipments = $em->getRepository('CSISEamBundle:Equipment')->searchByTags($term, self::SEARCH_EQUIPMENTS_TAGS_RESULTS);

        return $equipments;
    }

    /**
     * @Route("/search", name="search_results", options={"expose"=true})
     *                ?query=...
     * @Template("CSISEamBundle:Search:results.html.twig")
     */
    public function resultsAction (Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $query = $request->get('query');

        if ($query != '') {
            // Look for floors
            $floorRep = $em->getRepository('MyECPRoomingBundle:Floor');
            if($floor = $floorRep->findOneBy(array('name' => strtoupper($query)))) {
                return $this->redirect($this->generateUrl('floor_list', array('floor' => $floor->getName())));
            }

            // Look for members
            $slugQuery = $this->get('slug_service')->slug($query);
            $memberRep = $em->getRepository('MyECPCoreBundle:Member');
            $members = $memberRep->search($slugQuery);

            // Look for gangs
            $gangsRep = $em->getRepository('MyECPCoreBundle:Gang');
            $gangs = $gangsRep->search($query);

            // Look for disciplines
            $disciplinesRep = $em->getRepository('MyECPClassBundle:ClassDiscipline');
            $disciplines = $disciplinesRep->search($query);

            // Look for events
            $events = null;
        }
        else {
            $members = null;
            $gangs = null;
            $disciplines = null;
            $events = null;
        }

        return array(
            'query' => $query,
            'members' => $members,
            'gangs' => $gangs,
            'disciplines' => $disciplines,
            'events' => $events
        );
    }
}
