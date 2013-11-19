<?php
/**
 * Extendable Buddy Method class
 */
class BuddyMethod {
    /**
     * If the method has any required parameters, this function returns an array with the names of them
     *
     * @return array
     */
    public function getRequiredParameters()
    {
        return array();
    }

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

            }
        }
        return true;
    }
}