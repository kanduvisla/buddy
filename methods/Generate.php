<?php
/**
 * Buddy generate method
 *
 * This method can either:
 *
 * - Create a directory (according to a template)
 * - Create a file (according to a template)
 */

Class Generate extends BuddyMethod
{
    private $params;

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
        $this->params = $userParameters;
        foreach ($configParameters as $paramElement) {
            /* @var $paramElement SimpleXMLElement */
            foreach ($paramElement as $param) {
                /* @var $param SimpleXMLElement */
                switch($param->getName())
                {
                    case 'folder' :
                    {
                        // Create a folder:
                        $folderName = $this->parse((string)$param);
                        $this->createDir($folderName);
                        Buddy::out('Generated folder \'%s\'', array($folderName));
                        break;
                    }
                    case 'file' :
                    {
                        // Create a file:
                        $fileName = $this->parse((string)$param);
                        $attributes = $param->attributes();
                        $content = false;
                        if(isset($attributes['template']))
                        {
                            // Template mode:
                            $templateFile = (string)$attributes['template'];
                            if(!file_exists($templateFile))
                            {
                                Buddy::out('Error: file \'%s\' not found!', array($templateFile), Buddy::RED);
                            } else {
                                $content = file_get_contents($templateFile);
                            }
                        } elseif(isset($attributes['node']))
                        {
                            // Node mode:
                            $nodeName = (string)$attributes['node'];
                            $node = $paramElement->xpath($nodeName);
                            if(count($node) == 1) {
                                $content = (string)$node[0];
                            } elseif(count($node) == 0) {
                                Buddy::out('Error: node \'%s\' not found!', array($nodeName), Buddy::RED);
                            } else {
                                Buddy::out('Error: node \'%s\' can only appear once!', array($nodeName), Buddy::RED);
                            }
                        }
                        if($content === false)
                        {
                            Buddy::out('Error: cannot generate file \'%s\': there is no content.', array($fileName), Buddy::RED);
                        } else {
                            // Now we've got the content, now generate the file:
                            // Parse the content:
                            $content = $this->parse($content);
                            $this->createFile($fileName, $content);
                            Buddy::out('Generated file \'%s\'', array($fileName));
                            break;
                        }
                    }
                }

            }
        }
        return true;
    }

    /**
     * This function parses a string and returns the result.
     *
     * @param $str
     * @return mixed
     */
    private function parse($str)
    {
        // Replace the placeholders:
        preg_match_all('/\{\{(.*)\}\}/mU', $str, $matches);
        if(count($matches) == 2)
        {
            foreach($matches[0] as $index => $match) {
                $pattern = $matches[1][$index];
                $value = false;
                if(isset($this->params[$pattern]))
                {
                    $value = $this->params[$pattern];
                }
                // Check for modifiers:
                if(strpos($pattern, ':') !== false)
                {
                    $a = explode(':', $pattern);
                    if(count($a) == 2) {
                        switch($a[1])
                        {
                            case 'uppercase' :
                            {
                                $value = strtoupper($this->params[$a[0]]);
                                break;
                            }
                            case 'lowercase' :
                            {
                                $value = strtolower($this->params[$a[0]]);
                                break;
                            }
                            case 'ucfirst' :
                            {
                                $value = ucfirst(strtolower($this->params[$a[0]]));
                                break;
                            }
                        }
                    }
                }
                if($value === false) {
                    Buddy::out('Error: Can\'t resolve pattern \'%s\'', array($pattern), Buddy::RED);
                } else {
                    $str = str_replace($match, $value, $str);
                }
            }
        }
        return $str;
    }

    /**
     * Create a directory (including it's subdirectories)
     *
     * @param $dir
     */
    private function createDir($dir)
    {
        $path = explode('/', $dir);
        $fullpath = '';
        foreach($path as $chunk)
        {
            if(!file_exists($fullpath.$chunk))
            {
                mkdir($fullpath.$chunk);
            }
            $fullpath .= $chunk.'/';
        }
    }

    /**
     * Create a file (including it's subdirectories)
     *
     * @param $file
     * @param $content
     */
    private function createFile($file, $content)
    {
        $path = explode('/', $file);
        $fileName = array_pop($path);
        // Create the directory if it doesn't already exist:
        $this->createDir(implode('/', $path));
        file_put_contents($file, $content);
    }
}