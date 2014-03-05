<?php

namespace Inmarelibero\FirestarterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFrontendBundleCommand extends ContainerAwareCommand
{
    protected $output,
        $printHelper,
        $dialog
    ;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('inmarelibero_firestarter:_generate_frontend_bundle')

            ->setDescription('Generate Frontend Bundle')

            ->setHelp(<<<EOF
The <info>inmarelibero_firestarter:_generate_frontend_bundle</info> command generates the frontend bundle

  <info>php app/console inmarelibero_firestarter:_generate_frontend_bundle</info>

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

        // textFileHelper
        $this->textFileHelper = $this->getContainer()->get('inmarelibero_firestarter.text_file_helper');

        // dialogHelper
        $this->dialog = $this->getHelperSet()->get('dialog');

        $this->output = $output;

        $this->printHelper->printHeader('Creating the frontend bundle', 1);

        $this->printHelper->printSuccessMessage("Creating the frontend bundle");

        $bundleNamespace = $this->createBundle();

        $this->fixDefaultController($bundleNamespace);

        $this->printHelper->printSuccessMessage("Adding layout.html.twig");
        $this->addTemplates($bundleNamespace);

        $this->printHelper->printSuccessMessage("Frontend bundle created successfully");
    }

    /**
     * Creates the bundle
     */
    protected function createBundle()
    {
        $returnCode = null;

        while($returnCode !== 0) {
            $namespace = $this->dialog->ask(
                $this->output,
                'Please enter the bundle name (eg: Foo/BarBundle): '
            );

            $command = $this->getApplication()->find('generate:bundle');
            $arrayInput = new ArrayInput(array(
                'command' => $command->getName(),
                '--namespace' => $namespace,
                '--bundle-name' => preg_replace('#/#', '', $namespace),
                '--dir' => $this->getContainer()->getParameter('kernel.root_dir').'/../src',
                '--format' => 'annotation',
                '--structure' => true,
                '--format' => 'annotation',
            ));
            $arrayInput->setInteractive(false);

            try {
                $returnCode = $command->run($arrayInput, $this->output);

            } catch (\InvalidArgumentException $e) {
                $this->printHelper->printError($e->getMessage(), true);
            }
        }

        return $namespace;
    }

    /**
     * Removes the parameter from the default action
     *
     * @param $bundleNamespace
     */
    protected function fixDefaultController($bundleNamespace)
    {
        $file = $this->getBundleRootDir($bundleNamespace).'/Controller/DefaultController.php';

        $this->textFileHelper->replaceString($file, "/hello/{name}", "/");
        $this->textFileHelper->replaceString($file, "'name' => \$name", "");
        $this->textFileHelper->replaceString($file, "\$name", "");
    }

    /**
     *
     *
     * @param $bundleNamespace
     */
    protected function addTemplates($bundleNamespace)
    {
        /*
         * add layout.html.twig
         */
        $sourceFile = __DIR__.'/../Assets/src/Foo/BarBundle/Resources/views/layout.html.twig';
        $destFile = $this->getBundleRootDir($bundleNamespace).'/Resources/views/layout.html.twig';

        $this->getContainer()->get('inmarelibero_firestarter.file_helper')->copyFile($sourceFile, $destFile);

        /*
         * add Default/index.html.twig
         */
        $sourceFile = __DIR__.'/../Assets/src/Foo/BarBundle/Resources/views/Default/index.html.twig';
        $destFile = $this->getBundleRootDir($bundleNamespace).'/Resources/views/Default/index.html.twig';

        $this->getContainer()->get('inmarelibero_firestarter.file_helper')->copyFile($sourceFile, $destFile);

        $this->textFileHelper->replaceString($destFile, "FooBarBundle", $this->getBundleNameFromNamesapce($bundleNamespace));
    }

    /**
     * Return src/Foo/BarBundle dir
     *
     * @param $bundleNamespace
     * @return string
     */
    private function getBundleRootDir($bundleNamespace)
    {
        return $this->getContainer()->getParameter('kernel.root_dir').'/../src/'.$bundleNamespace;
    }

    private function getBundleNameFromNamesapce($bundleNamespace)
    {
        return preg_replace("#/#", "", $bundleNamespace);
    }
}