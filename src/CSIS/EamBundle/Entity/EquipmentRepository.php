<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use CSIS\UserBundle\Entity\User;

/**
 * EquipmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EquipmentRepository extends EntityRepository
{
    public function findByOwners($user) {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbByOwners($qb, $user);

        return $qb->getQuery()->getResult();
    }

    public function findAllOrderByDesignation() {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbOrderByDesignation($qb);

        return $qb->getQuery()->getResult();
    }

    public function findByOwnersOrderByEditDate(User $user) {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbByOwners($qb, $user);
        $qb = $this->qbOrderByEditDate($qb);

        return $qb->getQuery()->getResult();
    }

    public function findByOwnersOrderByDesignationPaginated(User $user, $start, $limit) {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbByOwners($qb, $user);
        $qb = $this->qbOrderByDesignation($qb);
        $qb = $this->qbPaginate($qb, $start, $limit);

        return new Paginator($qb);
    }

    public function findByOwnerPaginated(User $user, $start, $limit) {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbByOwners($qb, $user);
        $qb = $this->qbPaginate($qb, $start, $limit);

        return new Paginator($qb);
    }

    public function findByOwnersOrderByEditDatePaginated(User $user, $start, $limit) {
        $qb = $this->createQueryBuilder('e');
        $qb = $this->qbByOwners($qb, $user);
        $qb = $this->qbOrderByEditDate($qb);
        $qb = $this->qbPaginate($qb, $start, $limit);

        return new Paginator($qb);
    }

    private function qbByOwners(QueryBuilder $qb, User $user) {
        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GEST_ESTAB')) {
            return $qb;
        } elseif ($user->hasRole('ROLE_GEST_LAB') && $user->getInstitution()) {
            $institution = $user->getInstitution();

            return $qb->leftJoin('e.laboratory', 'l')
                ->leftJoin('l.institution', 'i')
                ->orWhere(':user MEMBER OF e.owners')
                ->orWhere(':user MEMBER OF l.owners')
                ->orWhere(':user MEMBER OF i.owners')
                ->orWhere('l.institution = :institution')
                ->setParameter('institution', $institution)
                ->setParameter('user', $user);

        } elseif ($user->hasRole('ROLE_GEST_EQUIP') && $user->getLab()) {
            $laboratory = $user->getLab();

            return $qb->leftJoin('e.laboratory', 'l')
                ->leftJoin('l.institution', 'i')
                ->orWhere(':user MEMBER OF e.owners')
                ->orWhere(':user MEMBER OF l.owners')
                ->orWhere(':user MEMBER OF i.owners')
                ->orWhere('e.laboratory = :laboratory')
                ->setParameter('laboratory', $laboratory)
                ->setParameter('user', $user);

        } else {
            return $qb->leftJoin('e.laboratory', 'l')
                      ->leftJoin('l.institution', 'i')
                      ->orWhere(':user MEMBER OF e.owners')
                      ->orWhere(':user MEMBER OF l.owners')
                      ->orWhere(':user MEMBER OF i.owners')
                      ->setParameter('user', $user);
        }
    }

    private function qbOrderByDesignation(QueryBuilder $qb) {
        return $qb->orderBy('e.designation', 'ASC');
    }

    private function qbOrderByEditDate(QueryBuilder $qb) {
        return $qb->orderBy('e.lastEditDate', 'DESC');
    }

    private function qbPaginate(QueryBuilder $qb, $start, $limit) {
        if ($start < 1) {
            throw new \InvalidArgumentException('L\'argument $start ne peut être inférieur à 1 (valeur : "' . $start . '").');
        }

        return $qb->setFirstResult(($start - 1) * $limit)
                        ->setMaxResults($limit);
    }

    public function findSearchedEquipments($superSearch) {
        $stringResult = "SELECT e FROM CSISEamBundle:Equipment e ";
        $stringResult .= "INNER JOIN e.equipmentTags et ";
        $stringResult .= "WHERE ";
        $stringResult .= $superSearch->getForm1()->getOpen();
        $stringResult .= " :tag1 = et.tag";


        if ($superSearch->getForm2()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm2()->getBooleans();
            $stringResult .= " " . $superSearch->getForm2()->getOpen();
            $stringResult .= " :tag2 = et.tag";
            $stringResult .= " " . $superSearch->getForm2()->getClose();
        }

        if ($superSearch->getForm3()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm3()->getBooleans();
            $stringResult .= " " . $superSearch->getForm3()->getOpen();
            $stringResult .= " :tag3 = et.tag";
            $stringResult .= " " . $superSearch->getForm3()->getClose();
        }

        if ($superSearch->getForm4()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm4()->getBooleans();
            $stringResult .= " " . $superSearch->getForm4()->getOpen();
            $stringResult .= " :tag4 = et.tag";
            $stringResult .= " " . $superSearch->getForm4()->getClose();
        }

        if ($superSearch->getForm5()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm5()->getBooleans();
            $stringResult .= " " . $superSearch->getForm5()->getOpen();
            $stringResult .= " :tag5 = et.tag";
            $stringResult .= " " . $superSearch->getForm5()->getClose();
        }

        if ($superSearch->getForm6()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm6()->getBooleans();
            $stringResult .= " " . $superSearch->getForm6()->getOpen();
            $stringResult .= " :tag6 = et.tag";
            $stringResult .= " " . $superSearch->getForm6()->getClose();
        }

        if ($superSearch->getForm7()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm7()->getBooleans();
            $stringResult .= " " . $superSearch->getForm7()->getOpen();
            $stringResult .= " :tag7 = et.tag";
            $stringResult .= " " . $superSearch->getForm7()->getClose();
        }

        if ($superSearch->getForm8()->getTag() != "") {
            $stringResult .= " " . $superSearch->getForm8()->getBooleans();
            $stringResult .= " " . $superSearch->getForm8()->getOpen();
            $stringResult .= " :tag8 = et.tag";
            $stringResult .= " " . $superSearch->getForm8()->getClose();
        }

        $query = $this->_em->createQuery($stringResult);

        $repo = $this->_em->getRepository('CSISEamBundle:Tag');
        $tag = $repo->findOneByTag($superSearch->getForm1()->getTag());
        if ($tag)
            $query->setParameter('tag1', $tag->getId());
        else
            $query->setParameter('tag1', '');
        if ($superSearch->getForm2()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm2()->getTag());
            if ($tag)
                $query->setParameter('tag2', $tag->getId());
            else
                $query->setParameter('tag2', '');
        }

        if ($superSearch->getForm3()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm3()->getTag());
            if ($tag)
                $query->setParameter('tag3', $tag->getId());
            else
                $query->setParameter('tag3', '');
        }

        if ($superSearch->getForm4()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm4()->getTag());
            if ($tag)
                $query->setParameter('tag4', $tag->getId());
            else
                $query->setParameter('tag4', '');
        }

        if ($superSearch->getForm5()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm5()->getTag());
            if ($tag)
                $query->setParameter('tag5', $tag->getId());
            else
                $query->setParameter('tag5', '');
        }

        if ($superSearch->getForm6()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm6()->getTag());
            if ($tag)
                $query->setParameter('tag6', $tag->getId());
            else
                $query->setParameter('tag6', '');
        }

        if ($superSearch->getForm7()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm7()->getTag());
            if ($tag)
                $query->setParameter('tag7', $tag->getId());
            else
                $query->setParameter('tag7', '');
        }

        if ($superSearch->getForm8()->getTag() != "") {
            $tag = $repo->findOneByTag($superSearch->getForm8()->getTag());
            if ($tag)
                $query->setParameter('tag8', $tag->getId());
            else
                $query->setParameter('tag8', '');
        }

        $equipments = $query->getResult();
        return $equipments;

//return $stringResult;
    }

    public function searchBarre($s) {
        $stringResult = "SELECT e FROM CSISEamBundle:Equipment e ";
        $stringResult .= "WHERE e.designation LIKE :s 
                          OR e.description LIKE :s 
                          OR e.brand LIKE :s
                          OR e.type LIKE :s";

        $query = $this->_em->createQuery($stringResult);
        $query->setParameter('s', "%" . $s . "%");

        return $query->getResult();
    }

    /**
     * @param Laboratory $laboratory
     *
     * @return bool
     */
    public function isLaboratoryUsed(Laboratory $laboratory)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('count(e)')
            ->where('e.laboratory = :laboratory')
            ->setParameter('laboratory', $laboratory);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function searchByName($pattern, $max_results = 200)
    {
        return $this->createQueryBuilder('e')
                ->where('LOWER(e.designation) LIKE :pattern')
                ->setParameter('pattern', '%'.strtolower($pattern).'%')
                ->setMaxResults($max_results)
                ->getQuery()
                ->getResult();
    }

    public function searchByTags($pattern, $max_results = 200)
    {
        $terms = explode(" ", $pattern);
        $search = "LOWER(t.tag) LIKE '" . implode("%' OR LOWER(t.tag) LIKE '", $terms) . "%'";

        return $this->createQueryBuilder('e')
                ->join('e.tags', 't')
                ->where($search)
                ->andWhere('t.status = :accepted')
                ->setParameter('accepted', Tag::ACCEPTED)
                ->setMaxResults($max_results)
                ->getQuery()
                ->getResult();
    }
}
