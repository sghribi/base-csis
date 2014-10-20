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
            ->setName('csis:users:create')
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

            $user = $em->getRepository('CSISUserBundle:User')->findOneBy(array('email' => $people->getEmail()));

            if ($user) {
                $output->writeln(sprintf('User with email <comment>%s</comment> already exists', $people->getEmail()));
                continue;
            }

            $output->writeln(sprintf('Created user with email <comment>%s</comment>', $people->getEmail()));

            $user = new User();
            $user->setFirstName($people->getFirstName());
            $user->setLastName($people->getName());
            $user->setEmail($people->getEmail());
            $user->setUrl($people->getUrl());
            $user->setPhoneNumber($people->getPhoneNumber());
            $user->setEnabled(false);

            // Hack to have unique username
            $suffix = '';
            do {
                $username = $user->getEmail() . $suffix;
                $suffix .= '_';
            } while ($em->getRepository('CSISUserBundle:User')->findOneBy(array('username' => $username )));
            $user->setUsername($username);

            // Password
            $length = 8;
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
            $password = substr(str_shuffle($chars), 0, $length);
            $user->setPlainPassword($password);

            $equipmentRepo= $em->getRepository('CSISEamBundle:Equipment');
            $equipments = $equipmentRepo->createQueryBuilder('e')
                ->where(':people MEMBER OF e.contacts')
                ->setParameter('people', $people)
                ->getQuery()
                ->getResult();

            /** @var Equipment $equipment **/
            foreach ($equipments as $equipment) {
                $output->writeln(sprintf('-> Add equipment : %s', $equipment->getDesignation()));
                $equipment->addOwner($user);
                $em->persist($equipment);
            }

            $em->persist($user);

            $em->flush();
        }
    }
}
