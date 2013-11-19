<?php

/**
 * Buddy delete method
 *
 * This method can either:
 *
 * - Empty a directory
 * - Delete a directory
 * - Delete files given a specific pattern
 */

Class Togglecode extends BuddyMethod
{
    /**
     * This is the run-method that does the magic. It's provided with the configuration-parameters, and
     * the user parameters (command line for example).
     *
     * The method returns true on success or false on failure.
     *
     * @param array $configParameters   Array with SimpleXMLElements
     * @param array $userParameters
     * @return bool
     */
    public function run($configParameters = array(), $userParameters = array())
    {
        foreach($configParameters as $param)
        {
            /* @var $param SimpleXMLElement */
            if($param->file && $param->line) {
                // Single line mode
                $file = (string)$param->file;
                $line = (string)$param->line;
                if(file_exists($file)) {
                    Buddy::out('Modifying \'%s\'...', array($file));
                    $atBegin = isset($param->line['begin']) ? $param->line['begin'] == 1 : false;
                    $this->toggleLine($file, $line, $atBegin);
                } else {
                    Buddy::out('File \'%s\' not found', array($file), Buddy::RED);
                }
            } elseif($param->file && $param->code && $param->pattern)
            {
                // Code block mode
                $file = (string)$param->file;
                $code = (string)$param->code;
                $pattern = (string)$param->pattern;
                if(file_exists($file)) {
                    Buddy::out('Modifying \'%s\'...', array($file));
                    $before = isset($param->pattern['before']) ? $param->pattern['before'] == 1 : false;
                    $this->toggleCodeBlock($file, (string)$code, trim($pattern), $before);
                } else {
                    Buddy::out('File \'%s\' not found', array($file), Buddy::RED);
                }
            }

        }
        return true;
    }

    /**
     * Toggle a code block in a file
     *
     * @param $file
     * @param $code
     * @param $pattern
     * @param bool $before
     * @return bool
     */
    private function toggleCodeBlock($file, $code, $pattern, $before = false)
    {
        $content = file_get_contents($file);
        preg_match('/'.$pattern.'/miUs', $content, $matches);
        if(count($matches) > 1)
        {
            $match = $matches[0];
            if($before)
            {
                if(strpos($content, $code."\n".$match) === false)
                {
                    $content = str_replace($match, $code."\n".$match, $content);
                    Buddy::out('Inserted code');
                } else {
                    $content = str_replace($code."\n".$match, $match, $content);
                    Buddy::out('Removed code');
                }
            } else {
                if(strpos($content, $match."\n".$code) === false)
                {
                    $content = str_replace($match, $match."\n".$code, $content);
                    Buddy::out('Inserted code');
                } else {
                    $content = str_replace($match."\n".$code, $match, $content);
                    Buddy::out('Removed code');
                }
            }
        }
        return $this->saveFile($file, $content);
    }

    /**
     * Toggle a single line in a file
     *
     * @param $file
     * @param $line
     * @param bool $atBegin
     * @return bool
     */
    private function toggleLine($file, $line, $atBegin = true)
    {
        $content = file_get_contents($file);
        if(strpos($content, $line) === false)
        {
            if($atBegin)
            {
                $content = $line."\n".$content;
            } else {
                $content = $content."\n".$line;
            }
            Buddy::out('Inserted line');
        } else {
            if($atBegin)
            {
                $content = str_replace($line."\n", '', $content);
            } else {
                $content = str_replace("\n".$line, '', $content);
            }
            Buddy::out('Removed line');
        }
        return $this->saveFile($file, $content);
    }

    /**
     * Save a file
     *
     * @param $file
     * @param $content
     * @return bool
     */
    private function saveFile($file, $content)
    {
        if(file_put_contents($file, $content))
        {
            Buddy::out('File saved');
            return true;
        } else {
            Buddy::out('Cannot save file', array(), Buddy::RED);
        }
        return false;
    }
}