<?php

namespace Inmarelibero\FirestarterBundle\Service;

use Inmarelibero\FirestarterBundle\Service\FileHelper;

class TextFileHelper
{
    protected $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @param $file absolute path of the file to read/write
     * @param $line string representing the line to remove in the file
     */
    public function removeLine($file, $linesToRemove)
    {
        $handle = $this->fileHelper->readFile($file);

        // parse $linesToRemove argument
        if (!is_Array($linesToRemove)) {
            $linesToRemove = array($linesToRemove);
        }

        $output = "";

        while (!feof($handle)) {
            $readLine = fgets($handle);

            if (!in_array(trim($readLine), $linesToRemove)) {
                $output .= $readLine;
            }
        }

        fclose($handle);

        return $this->fileHelper->writeFile($file, $output);
    }

    /**
     * @param $file absolute path of the file to read/write
     * @param $line string representing the line to remove in the file
     */
    public function replaceString($file, $match, $replacement)
    {
        $handle = $this->fileHelper->readFile($file);

        $output = "";

        while (!feof($handle)) {
            $readLine = fgets($handle);

            $regex = "/".preg_quote($match, "/")."/";

            $output .= preg_replace($regex, $replacement, $readLine);
        }

        fclose($handle);

        return $this->fileHelper->writeFile($file, $output);
    }
}