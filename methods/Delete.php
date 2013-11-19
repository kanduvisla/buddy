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

Class Delete extends BuddyMethod
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
        foreach($configParameters as $paramElement)
        {
            foreach($paramElement as $param)
            {
                /* @var $param SimpleXMLElement */
                switch($param->getName())
                {
                    case 'folder' :
                    {
                        // Folder action:
                        if(isset($param['mode']))
                        {
                            switch($param['mode'])
                            {
                                case 'empty' :
                                {
                                    // Empty folder:
                                    $files = glob($param.'/*');
                                    Buddy::out('Found %d files for deletion in \'%s\'', array(count($files), (string)$param));
                                    foreach($files as $file)
                                    {
                                        // Delete the file:
                                        $this->deleteFile($file);
                                    }
                                    break;
                                }
                                case 'delete' :
                                {
                                    // Delete / deltree folder:
                                    $this->delTree((string)$param);
                                    break;
                                }
                            }
                        }
                        break;
                    }
                    case 'file' :
                    {
                        // File action:
                        $files = glob((string)$param);
                        Buddy::out('Pattern \'%s\' matches %d files', array((string)$param, count($files)));
                        foreach($files as $file)
                        {
                            // Delete the file:
                            $this->deleteFile($file);
                        }
                        break;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Delete a single file
     *
     * @param $file
     * @return bool
     */
    private function deleteFile($file)
    {
        if(file_exists($file)) {
            if(unlink($file)) {
                Buddy::out('File \'%s\' successfully deleted', array($file));
                return true;
            } else {
                Buddy::out('File \'%s\' cannot be deleted', array($file), Buddy::RED);
            }
        } else {
            Buddy::out('File \'%s\' not found', array($file), Buddy::RED);
        }
        return false;
    }

    /**
     * Delete a directory
     *
     * @param $dir
     * @return bool
     */
    private function deleteDir($dir)
    {
        if(file_exists($dir) && is_dir($dir))
        {
            if(rmdir($dir)) {
                Buddy::out('Directory \'%s\' successfully deleted', array($dir));
                return true;
            } else {
                Buddy::out('Directory \'%s\' cannot be deleted', array($dir), Buddy::RED);
            }
        } else {
            Buddy::out('Directory \'%s\' not found', array($dir), Buddy::RED);
        }
        return false;
    }

    /**
     * Delete a complete directory structure
     *
     * @param $dir
     */
    private function delTree($dir)
    {
        $files = glob($dir.'/*');
        Buddy::out('Found %d files for deletion in \'%s\'', array(count($files), $dir));
        foreach($files as $file)
        {
            if(is_file($file))
            {
                // Delete the file:
                $this->deleteFile($file);
            } elseif(is_dir($file))
            {
                // Delete the directory:
                $this->delTree($file);
            }
        }
        // Last but not least, delete the directory itself:
        $this->deleteDir($dir);
    }
}
 
