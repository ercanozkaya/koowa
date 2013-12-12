<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract User Session Container
 *
 * This class provides structured storage of session attributes using a name spacing character in the key. Be default
 * the namespace character is a dot ('.')
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User
 */
abstract class KUserSessionContainerAbstract extends KObjectArray implements KUserSessionContainerInterface
{
    /**
     * The root attribute session namespace
     *
     * The attributes are stored in the an $_SESSION[namespace] array to avoid conflicts.
     *
     * @see loadSession()
     * @var string
     */
    protected $_namespace;

    /**
     * The attribute namespace separator
     *
     * @var string
     */
    protected $_separator;

    /**
     * Constructor
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return  KUserSessionContainerAbstract
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the attribute session namespace
        $this->setNamespace($config->namespace);

        //Set the attribute session separator
        $this->_separator = $config->separator;

        //Load the session data
        $this->loadSession();
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'namespace' => '__koowa_'.$this->getIdentifier()->name,
            'separator' => '.',
        ));

        parent::_initialize($config);
    }

    /**
     * Get a an attribute
     *
     * @param   string  $identifier Attribute identifier, eg .foo.bar
     * @param   mixed   $default    Default value when the attribute doesn't exist
     * @return  mixed   The value
     */
    public function get($identifier, $default = null)
    {
       $result = $this->offsetGet($identifier);

        // If the value is null return the default
        if(is_null($result)) {
            $result = $default;
        }

        return $result;
    }

    /**
     * Set an attribute
     *
     * Valid types are strings, numbers, and objects that implement a __toString() method.
     *
     * @param   mixed   $identifier Attribute identifier, eg foo.bar
     * @param   string  $value      Attribute value
     * @return KUserSessionContainerAbstract
     */
    public function set($identifier, $value)
    {
        $this->offsetSet($identifier, $value);
        return $this;
    }

    /**
     * Check if an attribute exists
     *
     * @param   string  $identifier Attribute identifier, eg foo.bar
     * @return  boolean
     */
    public function has($identifier)
    {
        return $this->offsetExists($identifier);
    }

    /**
     * Removes an attribute
     *
     * @param string $identifier Attribute identifier, eg foo.bar
     * @return KUserSessionContainerAbstract
     */
    public function remove($identifier)
    {
        $this->offsetUnset($identifier);
        return $this;
    }

    /**
     * Clears out all attributes
     *
     * @return KUserSessionContainerAbstract
     */
    public function clear()
    {
        $this->_data = array();
        return $this;
    }

    /**
     * Adds new attributes the active session.
     *
     * @param array $attributes An array of attributes
     * @return KUserSessionContainerAbstract
     */
    public function values(array $attributes)
    {
        foreach ($attributes as $key => $values) {
            $this->set($key, $values);
        }

        return $this;
    }

    /**
     * Set the session attributes namespace
     *
     * @param string $namespace The session attributes namespace
     * @return KUserSessionContainerAbstract
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Get the session attributes namespace
     *
     * @return string The session attributes namespace
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Get the session attributes separator
     *
     * @return string The session attribute separator
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Load the attributes from the $_SESSION global
     *
     * @param array $session The session data to load by reference. Will use $_SESSION by default.
     * @return KUserSessionContainerAbstract
     */
    public function loadSession(array &$session = null)
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        //Add the attributes by reference from the $_SESSION global
        if(!isset($session[$this->_namespace])) {
            $session[$this->_namespace] = array();
        }

        $this->_data = &$session[$this->_namespace];
        return $this;
    }

    /**
     * Get a an attribute
     *
     * @param   string  $identifier Attribute identifier, eg .foo.bar
     * @return  mixed   The value
     */
    public function offsetGet($identifier)
    {
        $keys = $this->_parseIdentifier($identifier);

        $result = $this->toArray();
        foreach($keys as $key)
        {
            if(array_key_exists($key, $result)) {
                $result = $result[$key];
            } else {
                $result = null;
                break;
            }
        }

        return $result;
    }

    /**
     * Set an attribute
     *
     * Valid types are strings, numbers, and objects that implement a __toString() method.
     *
     * @param   mixed   $identifier Attribute identifier, eg foo.bar
     * @param   string  $value      Attribute value
     * @return void
     */
    public function offsetSet($identifier, $value)
    {
        $keys = $this->_parseIdentifier($identifier);

        foreach(array_reverse($keys, true) as $key) {
            $value = array($key => $value);
        }

        $this->_data = $this->_mergeArrays($this->_data, $value);
    }

    /**
     * Check if an attribute exists
     *
     * @param   string  $identifier Attribute identifier, eg foo.bar
     * @return  boolean
     */
    public function offsetExists($identifier)
    {
        $keys = $this->_parseIdentifier($identifier);

        foreach($keys as $key)
        {
            if(array_key_exists($key, $this->_data)) {
                return true;
            };
        }

        return false;
    }

    /**
     * Unset an attribute
     *
     * @param string $identifier Attribute identifier, eg foo.bar
     * @return void
     */
    public function offsetUnset($identifier)
    {
        $keys = $this->_parseIdentifier($identifier);

        foreach($keys as $key)
        {
            if(array_key_exists($key, $this->_data))
            {
                unset($this->_data[$key]);
                break;
            };
        }
    }

    /**
     * Parse the variable identifier
     *
     * @param   string  $identifier Variable identifier
     * @return  array   The array of variables
     */
    protected function _parseIdentifier($identifier)
    {
        $parts = array();

        // Split the variable name into it's parts
        if(strpos($identifier, $this->_separator) !== false) {
            $parts = explode($this->_separator, $identifier);
        } else {
            $parts[] = $identifier;
        }

        return $parts;
    }

    /**
     * Merge two arrays recursively
     *
     * Matching keys' values in the second array overwrite those in the first array, as is the case with array_merge.
     *
     * Parameters are passed by reference, though only for performance reasons. They're not altered by this function and
     * the datatypes of the values in the arrays are unchanged.
     *
     * @param array $array1
     * @param array $array2
     * @return array    An array of values resulted from merging the arguments together.
     */
    protected function _mergeArrays( array &$array1, array &$array2 )
    {
        $args   = func_get_args();
        $merged = array_shift($args);

        foreach($args as $array)
        {
            foreach ( $array as $key => &$value )
            {
                if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ){
                    $merged [$key] = $this->_mergeArrays ( $merged [$key], $value );
                } else {
                    $merged [$key] = $value;
                }
            }
        }

        return $merged;
    }
}