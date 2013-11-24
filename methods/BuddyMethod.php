<?php
/**
 * Extendable Buddy Method class
 */
class BuddyMethod {
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
            /* @var $paramElement SimpleXMLElement */

            foreach($paramElement as $param)
            {
                /* @var $param SimpleXMLElement */

            }
        }
        return true;
    }
}