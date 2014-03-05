<?php

namespace Inmarelibero\FirestarterBundle\Service;


class FileHelper
{
    public function copyFile($source, $dest)
    {
        if (!file_exists($source)) {
            throw new \Exception("The file \"{$source}\" does not exist.");
        }

        return copy($source, $dest);
    }

    /**
     * Reads a $file and returns a resource
     *
     * @param $file
     * @return resource
     * @throws \Exception
     */
    public function readFile($file)
    {
        if (!file_exists($file) || !is_writable($file)) {
            throw new \Exception("The file \"{$file}\" does not exist or is not writable.");
        }

        // read file content
        return fopen($file, "r");
    }

    /**
     * Writes $output into a $file
     *
     * @param $file
     * @param $output
     * @return int
     * @throws \Exception
     */
    public function writeFile($file, $output)
    {
        if (!file_exists($file) || !is_writable($file)) {
            throw new \Exception("The file \"{$file}\" does not exist or is not writable.");
        }

        $handle = fopen($file, "w+");
        $result = fwrite($handle, $output);
        fclose($handle);

        return $result;
    }
} 