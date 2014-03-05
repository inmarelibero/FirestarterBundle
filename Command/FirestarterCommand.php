<?php

namespace Inmarelibero\FirestarterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class FirestarterCommand extends ContainerAwareCommand
{
    private $dialog,
        $input,
        $output,
        $printHelper,
        $commandHelper
    ;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('inmarelibero_firestarter:start')

            ->setDescription('Start the process for the customization of the Symfony Standard Edition')

            ->setHelp(<<<EOF
The <info>inmarelibero_firestarter:start</info> command starts the process for the customization of the Symfony Standard Edition.

  <info>php app/console inmarelibero_firestarter:start</info>

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
        $this->input = $input;
        $this->output = $output;
        $this->dialog = $this->getHelperSet()->get('dialog');

        // printHelper
        $this->printHelper = $this->getContainer()->get('inmarelibero_firestarter.print_helper');
        $this->printHelper->setDependencies($input, $output, $this->getHelperSet()->get('formatter'));

        // commandHelper
        $this->commandHelper = $this->getContainer()->get('inmarelibero_firestarter.command_helper');

        $output->writeln('<info>[InmareliberoFirestarterBundle starting...]</info>');

        /*
         * Install basic bundles
         */
        //$this->installBasicBundles();

        /*
         * Replace web/app_dev.php
         */
        $this->replaceAppDev();

        /*
         * Remove AcmeDemoBundle
         */
        $this->removeAcmeDemoBundle();

        /*
         * Create frontend bundle
         */
        $this->createFrontendBundle();

    }

    /**
     * Install basic bundles
     */
    private function installBasicBundles()
    {
        // first install composer.phar
        $this->installComposer();

        // @TODO: if composer is still not installed, break

        $basicBundles = Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/basic_bundles.yml'));

        $packages = array();

        foreach ($basicBundles['basic_bundles'] as $k => $v)

        if ($this->dialog->askConfirmation(
            $this->output,
            "<question>Install the bundle {$k}? [Y/n]</question>",
            true
        )) {
            // install bundle

            $packageString = $v['package_name'];
            if (isset($v['version'])) {
                $packageString .= ":{$v['version']}";
            }

            $process = $this->commandHelper->executeCommand("php composer.phar require {$packageString} --no-update");

            if (!$process->isSuccessful()) {
                $this->printHelper->printError("Unable to add the bundle {$packageString}");
            } else {
                $this->printHelper->printSuccessMessage("Bundle {$packageString} added successfully");
            }

            $packages[] = $v['package_name'];
        }

        $packagesString = implode($packages, " ");
        $process = $this->commandHelper->executeCommand("php composer.phar update {$packagesString}");

        if (!$process->isSuccessful()) {
            $this->printHelper->printError("Unable to install bundles {$packagesString}");
        } else {
            $this->printHelper->printSuccessMessage("Bundles {$packagesString} installed successfully");
        }
    }

    /**
     * Install composer.phar in the project root directory
     */
    public function installComposer()
    {
        $this->printHelper->printHeader('Installing Composer.phar', 1);

        if (file_exists($this->getContainer()->getParameter('kernel.root_dir').'/../composer.phar')) {
            $this->output->writeln("Skipping becasue composer.phar already exists in project root");
            return;
        }

        $process = $this->commandHelper->executeCommand("php -r \"readfile('https://getcomposer.org/installer');\" | php");


        if (!$process->isSuccessful()) {
            $this->printHelper->printError("Unable to install composer.phar with PHP. Trying with curl...");

            $process = $this->commandHelper->executeCommand("curl -sS https://getcomposer.org/installer | php");
        }

        if (!$process->isSuccessful()) {
            //throw new \RuntimeException($process->getErrorOutput());

            $this->printHelper->printError("Unable to install composer.phar with curl");
            $this->printHelper->printError("Unable to install composer.phar", true);

            return false;
        }

        $this->printHelper->printSuccessMessage("Composer installed successfully");
    }

    /**
     * Replaces web/app_dev.php
     *
     * @return bool
     */
    public function replaceAppDev()
    {
        $command = $this->getApplication()->find('inmarelibero_firestarter:_replace_app_dev');
        $returnCode = $command->run(new ArrayInput(array('command' => $command->getName())), $this->output);
    }

    /**
     * Removes AcmeDemoBundle
     *
     * @return bool
     */
    public function removeAcmeDemoBundle()
    {
        $command = $this->getApplication()->find('inmarelibero_firestarter:_remove_acme_demo_bundle');
        $returnCode = $command->run(new ArrayInput(array('command' => $command->getName())), $this->output);
    }

    /**
     * Create frontend bundle
     *
     * @return bool
     */
    public function createFrontendBundle()
    {
        $command = $this->getApplication()->find('inmarelibero_firestarter:_generate_frontend_bundle');
        $returnCode = $command->run(new ArrayInput(array('command' => $command->getName())), $this->output);
    }

}