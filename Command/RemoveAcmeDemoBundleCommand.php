<?php

namespace Inmarelibero\FirestarterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveAcmeDemoBundleCommand extends ContainerAwareCommand
{
    private $printHelper;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('inmarelibero_firestarter:_remove_acme_demo_bundle')

            ->setDescription('Removes AcmeDemoBundle')

            ->setHelp(<<<EOF
The <info>inmarelibero_firestarter:_remove_acme_demo_bundle</info> command removes the AcmeDemoBundle.

  <info>php app/console inmarelibero_firestarter:_remove_acme_demo_bundle</info>

More info at https://github.com/inmarelibero/FirestarterBundle
EOF
            )
        ;
    }

    /**
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // printHelper
        $this->printHelper = $this->getContainer()->get('inmarelibero_firestarter.print_helper');
        $this->printHelper->setDependencies($input, $output, $this->getHelperSet()->get('formatter'));

        $this->printHelper->printHeader('Removing AcmeDemoBundle', 1);

        $srcDir = $this->getContainer()->getParameter('kernel.root_dir').'/../src';

        if (!is_writable($srcDir)) {
            throw new \Exception("The folder {$srcDir} is not writable.");
        }

        $commandHelper = $this->getContainer()->get('inmarelibero_firestarter.command_helper');
        $textFileHelper = $this->getContainer()->get('inmarelibero_firestarter.text_file_helper');

        try {
            /*
             * delete src/Acme folder
             */
            // @TODO: check src/Acme exists
            $output->writeln('Removing src/Acme folder');
            $commandHelper->executeCommand("rm -rf {$srcDir}/Acme");

            /*
             * Delete bundle reference from app/AppKernel.php
             */
            $output->writeln('Removing AcmeDemoBundle reference fromapp/AppKernel.php');
            $textFileHelper->removeLine($this->getContainer()->getParameter('kernel.root_dir').'/AppKernel.php', "\$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();");

            /*
             * Delete bundle routes from app/config/routing_dev.php
             */
            $output->writeln('Deleting bundle routes from app/config/routing_dev.php');
            $textFileHelper->removeLine($this->getContainer()->getParameter('kernel.root_dir').'/config/routing_dev.yml', array(
                "# AcmeDemoBundle routes (to be removed)",
                "_acme_demo:",
                "resource: \"@AcmeDemoBundle/Resources/config/routing.yml\""
            ));
        } catch (\Exception $e) {
            $this->printHelper->printError($e->getMessage(), true);

            return false;
        }

        $this->printHelper->printSuccessMessage("AcmeDemoBundle removed successfully");
    }
}