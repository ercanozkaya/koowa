<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

require_once dirname(__FILE__).'/interface.php';
require_once dirname(__FILE__).'/locator/interface.php';
require_once dirname(__FILE__).'/locator/abstract.php';
require_once dirname(__FILE__).'/locator/library.php';
require_once dirname(__FILE__).'/registry/interface.php';
require_once dirname(__FILE__).'/registry/registry.php';
require_once dirname(__FILE__).'/registry/cache.php';

/**
 * Loader
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
class KClassLoader implements KClassLoaderInterface
{
    /**
     * The class locators
     *
     * @var array
     */
    protected $_locators = array();

    /**
     * The class container
     *
     * @var array
     */
    protected $_registry = null;

    /**
     * An associative array of basepaths
     *
     * @var array
     */
    protected $_basepaths = array();

    /**
     * The active basepath name
     *
     * @var  string
     */
    protected $_basepath = null;

    /**
     * Debug
     *
     * @var boolean
     */
    protected $_debug = false;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     */
    final private function __construct($config = array())
    {
        //Create the class registry
        if(isset($config['cache_enabled']) && $config['cache_enabled'])
        {
            $this->_registry = new KClassRegistryCache();

            if(isset($config['cache_namespace'])) {
                $this->_registry->setNamespace($config['cache_namespace']);
            }
        }
        else $this->_registry = new KClassRegistry();

        //Set the debug mode
        if(isset($config['debug'])) {
            $this->_debug = $config['debug'];
        }

        //Register the library locator
        $this->registerLocator(new KClassLocatorLibrary());

        //Register the Koowa Library namesoace 'K'
        $this->getLocator('library')->registerNamespace('K', dirname(dirname(__FILE__)));

        //Register the loader with the PHP autoloader
        $this->register();
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone()
    {
        throw new Exception("An instance of KClassLoader cannot be cloned.");
    }

    /**
     * Singleton instance
     *
     * @param  array  $config An optional array with configuration options.
     * @return KClassLoader
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
     * Registers this instance as an autoloader.
     *
     * @param boolean $prepend Whether to prepend the autoloader or not
     * @return void
     */
    public function register($prepend = false)
    {
        // Prepend parameter was added in 5.3.0 and the call does not work in 5.2 with it
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            spl_autoload_register(array($this, 'load'), true, $prepend);
        }
        else {
            spl_autoload_register(array($this, 'load'), true);
        }

        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
    }

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @return void
     *
     * @see spl_autoload_unregister();
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'load'));
    }

    /**
     * Load a class based on a class name
     *
     * @param string  $class    The class name
     * @throws RuntimeException If debug is enabled and the class could not be found in the file.
     * @return boolean  Returns TRUE if the class could be loaded, otherwise returns FALSE.
     */
    public function load($class)
    {
        $result = false;

        //Get the path
        $path = $this->getPath( $class, $this->_basepath);

        if ($path !== false)
        {
            if (!in_array($path, get_included_files()) && file_exists($path))
            {
                require $path;

                if($this->_debug)
                {
                    if(!$this->isDeclared($class))
                    {
                        throw new RuntimeException(sprintf(
                            'The autoloader expected class "%s" to be defined in file "%s".
                            The file was found but the class was not in it, the class name
                            or namespace probably has a typo.', $class, $path
                        ));
                    }
                }
            }
            else $result = false;
        }

        return $result;
    }

    /**
     * Enable or disable class loading
     *
     * If debug is enabled the class loader will throw an exception if a file is found but does not declare the class.
     *
     * @param bool|null $debug True or false. If NULL the method will return the current debug setting.
     * @return bool Returns the current debug setting.
     */
    public function debug($debug)
    {
        if($debug !== null) {
            $this->_debug = (bool) $debug;
        }

        return $this->_debug;
    }

    /**
     * Get the path based on a class name
     *
     * @param string $class    The class name
     * @param string $basepath The basepath name
     * @return string|boolean   Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function getPath($class, $basepath = null)
    {
        $result = false;

        //Switch the basepath
        $prefix = $basepath ? $basepath : $this->_basepath;

        if(!$this->_registry->has($prefix.'-'.$class))
        {
            //Locate the class
            foreach($this->_locators as $locator)
            {
                $path = $this->getBasepath($basepath);
                if(false !== $result = $locator->locate($class, $path)) {
                    break;
                };
            }

            //Also store if the class could not be found to prevent repeated lookups.
            $this->_registry->set($prefix.'-'.$class, $result);

        } else $result = $this->_registry->get($prefix.'-'.$class);

        return $result;
    }

    /**
     * Get the path based on a class name
     *
     * @param string $class    The class name
     * @param string $path     The class path
     * @param string $basepath The basepath name
     * @return void
     */
    public function setPath($class, $path, $basepath = null)
    {
        $prefix = $basepath ? $basepath : $this->_basepath;
        $this->_registry->set($prefix.'-'.$class, $path);
    }

    /**
     * Get the class registry object
     *
     * @return KClassRegistryInterface
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Register a class locator
     *
     * @param  KClassLocatorInterface $locator
     * @param  bool $prepend If true, the locator will be prepended instead of appended.
     * @return void
     */
    public function registerLocator(KClassLocatorInterface $locator, $prepend = false )
    {
        $array = array($locator->getType() => $locator);

        if($prepend) {
            $this->_locators = $array + $this->_locators;
        } else {
            $this->_locators = $this->_locators + $array;
        }
    }

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return KClassLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type)
    {
        $result = null;

        if(isset($this->_locators[$type])) {
            $result = $this->_locators[$type];
        }

        return $result;
    }

    /**
     * Get the registered class locators
     *
     * @return array
     */
    public function getLocators()
    {
        return $this->_locators;
    }

    /**
     * Register an alias for a class
     *
     * @param string  $class The original
     * @param string  $alias The alias name for the class.
     */
    public function registerAlias($class, $alias)
    {
        $alias = trim($alias);
        $class = trim($class);

        $this->_registry->alias($class, $alias);
    }

    /**
     * Get the registered alias for a class
     *
     * @param  string $class The class
     * @return array   An array of aliases
     */
    public function getAliases($class)
    {
        return array_search($class, $this->_registry->getAliases());
    }

    /**
     * Register a basepath by name
     *
     * @param string $name The name of the basepath
     * @param string $path The path
     * @return void
     */
    public function registerBasepath($name, $path)
    {
        $this->_basepaths[$name] = $path;
    }

    /**
     * Get a basepath by name
     *
     * @param string $name The name of the basepath
     * @return string The path
     */
    public function getBasepath($name)
    {
        return isset($this->_basepaths[$name]) ? $this->_basepaths[$name] : null;
    }

    /**
     * Set the active basepath by name
     *
     * @param string $name The name base path
     * @return KClassLoader
     */
    public function setBasepath($name)
    {
        $this->_basepath = $name;
        return $this;
    }

    /**
     * Get a list of basepaths
     *
     * @return array
     */
    public function getBasepaths()
    {
        return $this->_basepaths;
    }

    /**
     * Tells if a class, interface or trait exists.
     *
     * @param string $class
     * @return boolean
     */
    public function isDeclared($class)
    {
        return class_exists($class, false)
        || interface_exists($class, false)
        || (function_exists('trait_exists') && trait_exists($class, false));
    }
}
