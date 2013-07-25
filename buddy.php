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
    const VERSION   = '0.0.1';
    const RED       = "\033[0;31m";
    const WHITE     = "\033[1;37m";
    const GREEN     = "\033[0;32m";
    const GRAY      = "\033[0;37m";

    // Instance for the singleton:
    private static $_instance;

    // Variables:
    private $vars;

    /**
     * Constructor:
     */
    private function __construct()
    {
        // Default configuration:
        $this->vars = array(
            'configfile'  => 'buddy.xml'
        );
    }

    /**
     * Get the singleton instance
     * @return Buddy
     */
    public static function instance()
    {
        if(!self::$_instance) {
            self::$_instance = new Buddy();
            // Further initialisation:
        }
        return self::$_instance;
    }

    /**
     * Setter
     * @param $key      string  The name of the key
     * @param $value    mixed   The value
     */
    public function set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Getter
     * @param $key      string  The name of the key
     * @return mixed
     */
    public function get($key)
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
    public static function run($args)
    {
        Buddy::out('Buddy here! (v%s)', array(self::VERSION), self::WHITE);
        // Parse arguments:
        foreach($args as $arg)
        {
            $argument = split(':', $arg);
            if(count($argument) == 1) { $argument[] = 1; }
            Buddy::instance()->set($argument[0], $argument[1]);
        }
        // Check if there is a configuration file present:
        $configFile = Buddy::instance()->get('configfile');
        if(!file_exists($configFile))
        {
            Buddy::out('%s is not found. Baking a new one for ya...', array($configFile));
            $buddyXML = new SimpleXMLElement('<buddy></buddy>');
            $buddyXML->asXML('buddy.xml', $configFile);
            Buddy::out('%s created. You better start editing', array($configFile));
        } else {
            // Load config file and parse it:
            try {
                @$buddyXML = new SimpleXMLElement(file_get_contents($configFile));
            } catch(Exception $e) {
                Buddy::out("Cannot load %s, is that XML valid son?", array($configFile), self::RED);
                return;
            }
            Buddy::out('%s loaded. Start parsing...', array($configFile));
            Buddy::instance()->parse($buddyXML);
            Buddy::out('Have a nice day!');
        }
    }

    /**
     * Output some text to the console. This function takes additional parameters.
     * @param $str
     */
    public static function out($str, $args = array(), $color = self::GRAY)
    {
        echo $color.vsprintf($str, $args).self::GRAY;
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
        echo $question.': ';
        $handle = fopen ("php://stdin","r");
        Buddy::instance()->set($var, trim(fgets($handle)));
    }

    /**
     * @param $xml  SimpleXMLElement
     */
    public function parse($xml)
    {
        if(!$this->get('a')) {
            // No action is set, show a list of available actions:
            Buddy::out("\nbuddy a:[action] [params]\n");
            Buddy::out("Posible actions and their parameters:\n");
            foreach($xml->xpath('actions/action') as $actionXML)
            {
                Buddy::out(self::WHITE."\t".$actionXML['name'].self::GRAY."\n\t\t".$actionXML->description);
            }
        } else {

        }
    }
}

/*
 * Run, Buddy run!:
 */
array_shift($argv);
Buddy::run($argv);