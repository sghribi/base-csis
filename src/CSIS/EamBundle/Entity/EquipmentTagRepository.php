<?php

namespace CSIS\EamBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EquipmentTagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EquipmentTagRepository extends EntityRepository
{
    public function isTagUsed(Tag $tag)
    {
        $qb = $this->createQueryBuilder('et')
            ->select('count(et) as nb')
            ->where('et.tag = :tag')
            ->setParameter('tag', $tag);

        return ($qb->getQuery()->getSingleResult()['nb'] > 0);
    }
}
