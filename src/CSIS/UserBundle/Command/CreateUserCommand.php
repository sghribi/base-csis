<?php

namespace CSIS\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('csis:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('firstName', InputArgument::REQUIRED, 'The first name'),
                new InputArgument('lastName', InputArgument::REQUIRED, 'The last name'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ))
            ->setHelp(<<<EOT
The <info>csis:user:create</info> command creates a user:

  <info>php app/console csis:user:create matthieu</info>

This interactive shell will ask you for an email and then a password.

You can alternatively specify the email and password as the second and third arguments:

  <info>php app/console csis:user:create matthieu matthieu@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console csis:user:create admin --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console csis:user:create thibault --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $firstName  = $input->getArgument('firstName');
        $lastName   = $input->getArgument('lastName');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $manipulator = $this->getApplication()->getKernel()->getContainer()->get('csis.user.manager');
        $manipulator->create($username, $password, $email, $firstName, $lastName, !$inactive, $superadmin);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );
            $input->setArgument('password', $password);
        }

        if (!$input->getArgument('firstName')) {
            $firstName = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a firstName:',
                function($firstName) {
                    if (empty($firstName)) {
                        throw new \Exception('First name can not be empty');
                    }

                    return $firstName;
                }
            );
            $input->setArgument('firstName', $firstName);
        }

        if (!$input->getArgument('lastName')) {
            $lastName = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a lastName:',
                function($lastName) {
                    if (empty($lastName)) {
                        throw new \Exception('Last name can not be empty');
                    }

                    return $lastName;
                }
            );
            $input->setArgument('lastName', $lastName);
        }
    }
} 