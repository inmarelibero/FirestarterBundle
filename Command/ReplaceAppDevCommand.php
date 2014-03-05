<?php

namespace Inmarelibero\FirestarterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReplaceAppDevCommand extends ContainerAwareCommand
{
    private $printHelper;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('inmarelibero_firestarter:_replace_app_dev')

            ->setDescription('Replaces the web/app_dev.php')

            ->setHelp(<<<EOF
The <info>inmarelibero_firestarter:_replace_app_dev</info> command replaces web/app_dev.php.

  <info>php app/console inmarelibero_firestarter:_replace_app_dev</info>

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

        $this->printHelper->printHeader('Replacing web/app_dev.php', 1);

        $destDir = $this->getContainer()->getParameter('kernel.root_dir').'/../web';
        
        if (!is_writable($destDir)) {
            throw new \Exception("The folder {$destDir} is not writable.");
        }

        $sourceFile = __dir__.'/../Assets/web/app_dev.php';

        $output->writeln('Copying the new web/app_dev.php');

        $this->getContainer()->get('inmarelibero_firestarter.file_helper')->copyFile($sourceFile, $destDir.'/app_dev.php');

        $this->printHelper->printSuccessMessage("web/app_dev.php replaced successfully");
    }
}