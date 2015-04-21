<?php

namespace Qcm\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUserCommand
 */
class InstallCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('qcm:install')
            ->setDescription('Qcm installer.');
     }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing Qcm.</info>');
        $output->writeln('');

        $this
            ->install($input, $output)
        ;

        $output->writeln('<info>Qcm has been successfully installed.</info>');
    }

    protected function install(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Setting up database.</info>');

        $this->setupDatabase($input, $output);

        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');

        $this->setupAdmin($output);

        $output->writeln('');

        return $this;
    }

    protected function setupDatabase(InputInterface $input, OutputInterface $output)
    {
        $this
            ->runCommand('doctrine:database:create', $input, $output)
            ->runCommand('doctrine:schema:create', $input, $output)
            ->runCommand('assets:install', $input, $output);
    }
    protected function setupAdmin(OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $user = $this->getContainer()->get('qcm_core.controller.user')->createNew();

        $user->setUsername($dialog->ask(
            $output,
            '<question>Username</question> <comment>(admin)</comment>:',
            'admin'
        ));

        $user->setPlainPassword($dialog->askHiddenResponseAndValidate(
            $output,
            '<question>Password:</question>',
            function($answer) {
                if (empty($answer)) {
                    throw new \RunTimeException('Choose a password.');
                }

                if (strlen($answer) <= 2) {
                    throw new \RunTimeException('The password is too short.');
                }

                return $answer;
            }
        ));

        $user->setEmail($dialog->ask($output, '<question>Email:</question>'));
        $user->setRoles(array('ROLE_ADMIN'));
        $this->getContainer()->get('qcm_core.user_manager')->updatePassword($user);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();
    }

    protected function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApplication()
            ->find($command)
            ->run($input, $output)
        ;

        return $this;
    }
}
