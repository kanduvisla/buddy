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

    }
}
 
