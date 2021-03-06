<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Koowa constant, if true koowa is loaded
 */
define('KOOWA', 1);

/**
 * Koowa
 *
 * Loads classes and files, and provides metadata for Koowa such as version info
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library
 */
class Koowa
{
    /**
     * Koowa version
     *
     * @var string
     */
    const VERSION = '2.0.0';

    /**
     * Path to Koowa libraries
     *
     * @var string
     */
    protected $_path;

 	/**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     *
     * @param  array  $config An optional array with configuration options.
     */
    final private function __construct($config = array())
    {
        //Initialize the path
        $this->_path = dirname(__FILE__);

        //Load the legacy functions
        require_once $this->_path.'/legacy.php';

        //Setup the loader
        require_once $this->_path.'/class/loader.php';

        if (!isset($config['class_loader'])) {
            $config['class_loader'] = KClassLoader::getInstance($config);
        }

        //Setup the factory
        KObjectManager::getInstance($config);
    }

	/**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone() { }

	/**
     * Singleton instance
     *
     * @param  array  $config An optional array with configuration options.
     * @return Koowa
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL) {
            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Get the version of the Koowa library
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Get path to Koowa libraries
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
}
