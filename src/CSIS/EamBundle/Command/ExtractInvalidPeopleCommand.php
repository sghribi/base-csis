<?php

namespace CSIS\EamBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator;

class ExtractInvalidPeopleCommand extends Command
{
    protected function configure ()
    {
        $this->setName('csis:people:extract-invalid')
            ->setDescription('Extracts invalid people from database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Récupération et suppression des contacts avec une adresse email invalide.');

        /** @var Container $container */
        $container =  $this->getApplication()->getKernel()->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine.orm.default_entity_manager');
        /** @var Validator $validator */
        $validator = $container->get('validator');

        $equipmentRepo = $em->getRepository('CSISEamBundle:Equipment');
        $emailConstraint = new Email(array('checkHost' => true, 'checkMX' => true));
        $peoples = $em->getRepository('CSISEamBundle:People')->findAll();

        foreach ($peoples as $people) {
            $errors = $validator->validateValue($people->getEmail(), $emailConstraint);

            if (count($errors) > 0) {
                $output->writeln('');
                $output->writeln('Suppression du contact avec comme email invalide : "' . $people->getEmail() . '"');

                $equipments = $equipmentRepo->findByContact($people);

                foreach ($equipments as $equipment) {
                    $output->writeln(' * ' . $equipment->getDesignation());
                }

                $em->remove($people);
            }
        }

        $em->flush();
    }
}
