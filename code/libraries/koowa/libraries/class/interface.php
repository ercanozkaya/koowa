<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Class Loader Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Class
 */
interface KClassLoaderInterface
{
    /**
     * Registers the loader with the PHP autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     * @see \spl_autoload_register();
     */
    public function register($prepend = false);

    /**
     * Unregisters the loader with the PHP autoloader.
     *
     * @see \spl_autoload_unregister();
     */
    public function unregister();

    /**
     * Load a class based on a class name
     *
     * @param  string   $class  The class name
     * @throws RuntimeException If debug is enabled and the class could not be found in the file.
     * @return boolean  Returns TRUE if the class could be loaded, otherwise returns FALSE.
     */
    public function load($class);

    /**
     * Enable or disable class loading
     *
     * If debug is enabled the class loader should throw an exception if a file is found but does not declare the class.
     *
     * @param bool|null $debug True or false. If NULL the method will return the current debug value.
     * @return bool Returns the current debug value.
     */
    public function debug($debug);

    /**
     * Get the path based on a class name
     *
     * @param string $class    The class name
     * @param string $basepath The basepath name
     * @return string|boolean   Returns canonicalized absolute pathname or FALSE of the class could not be found.
     */
    public function getPath($class, $basepath = null);

    /**
     * Set the path based for a class
     *
     * @param string $class    The class name
     * @param string $path     The class path
     * @param string $basepath The basepath name
     * @return void
     */
    public function setPath($class, $path, $basepath = null);

    /**
     * Register a class locator
     *
     * @param  KClassLocatorInterface $locator
     * @param  bool $prepend If true, the locator will be prepended instead of appended.
     * @return void
     */
    public function registerLocator(KClassLocatorInterface $locator, $prepend = false );

    /**
     * Get a registered class locator based on his type
     *
     * @param string $type The locator type
     * @return KClassLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type);

    /**
     * Get the registered adapters
     *
     * @return array
     */
    public function getLocators();

    /**
     * Register an alias for a class
     *
     * @param string  $class The original
     * @param string  $alias The alias name for the class.
     */
    public function registerAlias($class, $alias);

    /**
     * Get the registered alias for a class
     *
     * @param  string $class The class
     * @return array   An array of aliases
     */
    public function getAliases($class);

    /**
     * Register a basepath by name
     *
     * @param string $name The name of the basepath
     * @param string $path The path
     * @return void
     */
    public function registerBasepath($name, $path);

    /**
     * Get a basepath by name
     *
     * @param string $name The name of the application
     * @return string The path of the application
     */
    public function getBasepath($name);

    /**
     * Set the active basepath by name
     *
     * @param string $name The name base path
     * @return KClassLoader
     */
    public function setBasepath($name);

    /**
     * Get a list of basepaths
     *
     * @return array
     */
    public function getBasepaths();

    /**
     * Tells if a class, interface or trait exists.
     *
     * @param string $class
     * @return boolean
     */
    public function isDeclared($class);
}