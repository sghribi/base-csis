<?php

namespace CSIS\UserBundle\Command;

use CSIS\EamBundle\Entity\Equipment;
use CSIS\EamBundle\Entity\Institution;
use CSIS\EamBundle\Entity\Laboratory;
use CSIS\UserBundle\Entity\User;
use CSIS\EamBundle\Entity\People;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class PromoteContactsCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('csis:user:create')
            ->setDescription('Update contacts to users');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getApplication()->getKernel()->getContainer()->get('doctrine')->getManager();
        $peoples = $em->getRepository('CSISEamBundle:People')->findAll();

        /** @var People $people */
        foreach ($peoples as $people) {
            $user = new User();
            $user->setFirstName($people->getFirstName());
            $user->setLastName($people->getName());
            $user->setEmail($people->getEmail());
            $user->setUrl($people->getUrl());
            $user->setPhoneNumber($people->getPhoneNumber());
            $user->setEnabled(true);

            /**
             * S'occuper de :
             *
             * $lab = new Laboratory();
            $lab->getOwners();

            $eq = new Equipment();
            $eq->getOwners();
             */
        }

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }
} 