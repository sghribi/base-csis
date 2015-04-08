<?php

namespace Application\Migrations;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Entity\EquipmentRepository;
use CSIS\UserBundle\Entity\User;
use CSIS\UserBundle\Entity\UserRepository;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408190501 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function preUp(Schema $schema)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository('CSISUserBundle:User');
        /** @var EquipmentRepository $equipmentRepository */
        $equipmentRepository = $em->getRepository('CSISEamBundle:Equipment');

        $users = $userRepository
            ->createQueryBuilder('u')
            ->where('u.lab IS NULL')
            ->andWhere('u.institution IS NULL')
            ->getQuery()
            ->getResult();

        $withoutEquipmentWithoutLaboratory = array();
        $withoutEquipmentWithMultiplesLaboratory = array();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GEST_ESTAB')) {
                continue;
            }

            $equipments = $equipmentRepository
                ->createQueryBuilder('e')
                ->where(':user MEMBER OF e.owners')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            if (count($equipments) == 0) {
                $withoutEquipmentWithoutLaboratory[] = $user->getEmailCanonical();
                continue;
            }

            $laboratories = array();

            /** @var Equipment $equipment */
            foreach ($equipments as $equipment) {
                $laboratories[] = $equipment->getLaboratory();
            }

            $laboratories = array_unique($laboratories);

            if (count($laboratories) > 1) {
                $withoutEquipmentWithMultiplesLaboratory[] = $user->getEmailCanonical();
                continue;
            }

            $laboratory = array_shift($laboratories);

            $user->setLab($laboratory);
        }

        if (count($withoutEquipmentWithMultiplesLaboratory) > 0) {
            echo sprintf('Des contacts dont on ne peut déterminer le laboratoire de présence sont présents dans la base : %s' . PHP_EOL, implode(PHP_EOL . ' - ', $withoutEquipmentWithMultiplesLaboratory));
        }

        if (count($withoutEquipmentWithoutLaboratory) > 0)  {
            echo sprintf('Des contacts sans équipement sont présents dans la base : %s', implode(PHP_EOL . ' - ', $withoutEquipmentWithoutLaboratory));
        }

        // Flush to handle slug
        $em->flush();
    }
}
