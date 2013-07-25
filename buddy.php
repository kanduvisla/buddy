<?php
/*
 * Buddy
 * -----
 * A buddy for your daily PHP programming
 *
 * Buddy is a CLI program created with PHP to provide some help for boring,
 * repetitive task, so you can focus on the fun stuff.
 *
 * Features include:
 *
 * - File management, like cleaning of cache directories and stuff.
 * - File manipulation, for toggling variables for example.
 * - Code generation, for creating boilerplate plugins for your framework of choice.
 *
 * Buddy loads an XML file which contains all the possible options.
 */

/*
 * Define classes
 */
Class Buddy {
    // Constants:
    const VERSION = '0.0.1';

    // Instance for the singleton:
    static $_instance;

    // Variables:
    private $vars;

    private function __construct()
    {
        if(!self::$_instance) {
            self::$_instance = new Buddy();
            // Further initialisation:
            $this->vars = array();
        }
        return self::$_instance;
    }

    /**
     * Get the singleton instance
     * @return Buddy
     */
    public static function instance()
    {
        return self::$_instance;
    }

    /**
     * Magic getters and setters
     */
    public function __call($method, $args)
    {

    }

    /**
     * Setter
     * @param $key      string  The name of the key
     * @param $value    mixed   The value
     */
    public function setData($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Getter
     * @param $key      string  The name of the key
     * @return mixed
     */
    public function getData($key)
    {
        if(!array_key_exists($key, $this->vars)) {
            return false;
        } else {
            return $this->vars[$key];
        }
    }

    /**
     * Entry point
     */
    public static function run()
    {
        Buddy::out('Buddy here! (v%s)', self::VERSION);
        // Check if there is a configuration file present:
        if(!file_exists('buddy.xml'))
        {
            Buddy::out('buddy.xml is not found. Baking a new one for ya...');
            $buddyXML = new SimpleXMLElement('<buddy></buddy>');
            $buddyXML->asXML('buddy.xml');
        }
    }

    /**
     * Output some text to the console. This function takes additional parameters.
     * @param $str
     */
    public static function out($str)
    {
        $args = func_get_args();
        array_shift($args);
        vprintf($str, $args);
        echo "\n";
    }

    /**
     * Ask user input
     * @param $var      string  The name of the parameter
     * @param $question mixed  The question asked
     */
    public static function in($var, $question = false)
    {
        if(!$question) { $question = ucfirst($var); }
        $handle = fopen ("php://stdin","r");
        self::instance()->setData($var, trim(fgets($handle)));
    }
}

/*
 * Run, Buddy run!:
 */
Buddy::run();