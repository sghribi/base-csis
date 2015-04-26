<?php

namespace Application\Migrations;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Entity\EquipmentRepository;
use CSIS\EamBundle\Entity\Tag;
use CSIS\EamBundle\Entity\TagRepository;
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
class Version20150426133900 extends AbstractMigration implements ContainerAwareInterface
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

    /**
     * {@inheritdoc}
     */
    public function preUp(Schema $schema)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $tags = $em->getRepository('CSISEamBundle:Tag')->findBy(array('status' => Tag::PENDING));

        foreach ($tags as $tag) {
            $tag->setStatus(Tag::ACCEPTED);
        }

        $em->flush();
    }
}
