<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Loader Adapter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Loader
 */
abstract class KClassLocatorAbstract implements KClassLocatorInterface
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = '';

    /**
     * Namespace/directory pairs to search
     *
     * @var array
     */
    protected $_namespaces = array();

	/**
     * Constructor.
     *
     * @param  array  $config An optional array with configuration options.
     */
    public function __construct( $config = array())
    {
        if(isset($config['namespaces']))
        {
            $namespaces = (array) $config['namespaces'];
            foreach($namespaces as $namespace => $path) {
                $this->registerNamespace($namespace, $path);
            }
        }
    }

    /**
     * Get the type
     *
     * @return string	Returns the type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Register a namespace
     *
     * @param  string $namespace
     * @param  string $path The location of the namespace
     * @return KClassLocatorInterface
     */
    public function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace, '\\');
        $this->_namespaces[$namespace] = $path;

        krsort($this->_namespaces, SORT_STRING);

        return $this;
    }

    /**
     * Registers an array of namespaces
     *
     * @param array $namespaces An array of namespaces (namespaces as keys and location as value)
     * @return KClassLocatorInterface
     */
    public function registerNamespaces($namespaces)
    {
        foreach ($namespaces as $namespace => $path) {
            $this->registerNamespace($namespace, $path);
        }

        return $this;
    }

    /**
     * Get a the namespace paths
     *
     * @param string $namespace The namespace
     * @return string The namespace path
     */
    public function getNamespace($namespace)
    {
        $namespace = trim($namespace, '\\');
        return isset($this->_namespaces[$namespace]) ?  $this->_namespaces[$namespace] : null;
    }

    /**
     * Get the registered namespaces
     *
     * @return array An array with namespaces as keys and path as value
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }
}
