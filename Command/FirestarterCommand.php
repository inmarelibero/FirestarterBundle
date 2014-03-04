<?php

namespace Inmarelibero\FirestarterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Process;

class FirestarterCommand extends ContainerAwareCommand
{
    private $dialog;
    private $input;
    private $output;

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

        $this->printHelper = $this->getContainer()->get('inmarelibero_firestarter.print_helper');
        $this->printHelper->setInput($input);
        $this->printHelper->setOutput($output);
        $this->printHelper->setFormatter($this->getHelperSet()->get('formatter'));

        $output->writeln('<info>[InmareliberoFirestarterBundle starting...]</info>');

        $this->installBasicBundles();


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

            $process = $this->executeCommand("php composer.phar require {$packageString} --no-update");

            if (!$process->isSuccessful()) {
                $this->printHelper->printError("Unable to add the bundle {$packageString}");
            } else {
                $this->printHelper->printSuccessMessage("Bundle {$packageString} added successfully");
            }
        }


        //die('xxx');
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


        $process = $this->executeCommand("php -r \"readfile('https://getcomposer.org/installer');\" | php");


        if (!$process->isSuccessful()) {
            $this->printHelper->printError("Unable to install composer.phar with PHP. Trying with curl...");

            $process = $this->executeCommand("curl -sS https://getcomposer.org/installer | php");
        }

        if (!$process->isSuccessful()) {
            //throw new \RuntimeException($process->getErrorOutput());

            $this->printHelper->printError("Unable to install composer.phar with curl");
            $this->printHelper->printError("Unable to install composer.phar", true);

            return;
        }

        $this->printHelper->printSuccessMessage("Composer installed successfully");
    }

    /**
     * Execute a command writing on the $output the result in real time
     *
     * @param $command
     * @return Process
     */
    private function executeCommand($command)
    {
        chdir($this->getContainer()->getParameter('kernel.root_dir').'/..');

        $process = new Process($command);

        $process->setTimeout(360);

        $process->run(function ($type, $buffer) use (&$process) {
            if (Process::ERR === $type) {
                echo $buffer;
                $process->stop(3, SIGINT);
            } else {
                echo $buffer;
            }
        });

        return $process;
    }
}
















