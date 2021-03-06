<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Identifier
 *
 * Wraps identifiers of the form type:[//domain/]package.[.path].name in an object, providing public accessors and
 * methods for derived formats.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object
 *
 * @property string $name object name
 */
class KObjectIdentifier implements KObjectIdentifierInterface
{
    /**
     * The identifier
     *
     * @var string
     */
    protected $_identifier = '';

    /**
     * The identifier type [com|plg|mod]
     *
     * @var string
     */
    protected $_type = 'lib';

    /**
     * The identifier domain
     *
     * @var string
     */
    protected $_domain = '';

    /**
     * The identifier package
     *
     * @var string
     */
    protected $_package = '';

    /**
     * The identifier path
     *
     * @var array
     */
    protected $_path = array();

    /**
     * The identifier object name
     *
     * @var string
     */
    protected $_name = '';

     /**
     * The identifier class
     *
     * @var string
     */
    protected $_class = '';

    /**
     * The object config
     *
     * @var KObjectConfig
     */
    protected $_config = null;

    /**
     * Constructor
     *
     *
     * @param   string $identifier Identifier string or object in type://domain/package[.path].name format
     * @throws  KObjectExceptionInvalidIdentifier If the identifier cannot be parsed
     */
    public function __construct($identifier)
    {
        //Get the parts
        if(!is_array($identifier))
        {
            if(false === $parts = parse_url($identifier)) {
                throw new KObjectExceptionInvalidIdentifier('Identifier cannot be parsed : '.$identifier);
            }

            // Set the type
            $this->_type = isset($parts['scheme']) ? $parts['scheme'] : 'lib';

            //Set the domain
            if(isset($parts['host'])) {
                $this->_domain = $parts['host'];
            }

            // Set the path
            $this->_path = trim($parts['path'], '/');
            $this->_path = explode('.', $this->_path);

            // Set the extension (first part)
            $this->_package = array_shift($this->_path);

            // Set the name (last part)
            if(count($this->_path)) {
                $this->_name = array_pop($this->_path);
            }
        }
        else
        {
            $parts = $identifier;
            foreach ($parts as $key => $value) {
                $this->{'_'.$key} = $value;
            }
        }

        //Cache the identifier to increase performance
        $this->_identifier = $this->toString();
    }

	/**
	 * Serialize the identifier
	 *
	 * @return string 	The serialised identifier
	 */
	public function serialize()
	{
        $data = $this->toArray();
        $data['identifier'] = $this->_identifier;
        $data['class']      = $this->_class;

        return serialize($data);
	}

	/**
	 * Unserialize the identifier
	 *
	 * @param string 	$data The serialised identifier
	 */
	public function unserialize($data)
	{
	    $data = unserialize($data);

	    foreach($data as $property => $value) {
	        $this->{'_'.$property} = $value;
	    }
	}

    /**
     * Get the identifier type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the identifier domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * Get the identifier package
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Get the identifier package
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the identifier class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set the identifier class name
     *
     * @param  string $class
     * @return KObjectIdentifier
     */
    public function setClass($class)
    {
        $this->_class = $class;
        return $this;
    }

    /**
     * Get the config
     *
     * This function will lazy create a config object is one does not exist yet.
     *
     * @return KObjectConfig
     */
    public function getConfig()
    {
        if(!isset($this->_config)) {
            $this->_config = new KObjectConfig();
        }

        return $this->_config;
    }

    /**
     * Set the config
     *
     * @param   array    $data   A ObjectConfig object or a an array of configuration options
     * @param   boolean  $merge  If TRUE the data in $config will be merged instead of replaced. Default TRUE.
     * @return  KObjectIdentifierInterface
     */
    public function setConfig($data, $merge = true)
    {
        $config = $this->getConfig();

        if($merge) {
            $config->append($data);
        } else {
            $this->_config = new KObjectConfig($data);
        }

        return $this;
    }

    /**
     * Add a mixin
     *
     * @param mixed $decorator An object implementing ObjectMixinInterface, an ObjectIdentifier or an identifier string
     * @param array $config     An array of configuration options
     * @return KObjectIdentifierInterface
     * @see KObject::mixin()
     */
    public function addMixin($mixin, $config = array())
    {
        if ($mixin instanceof KObjectMixinInterface || $mixin instanceof KObjectIdentifier) {
            $this->getMixins()->append(array($mixin));
        } else {
            $this->getMixins()->append(array($mixin => $config));
        }

        return $this;
    }

    /**
     * Get the mixin registry
     *
     * @return KObjectConfig
     */
    public function getMixins()
    {
        if(!isset($this->getConfig()->mixins)) {
            $this->getConfig()->append(array('mixins' => array()));
        }

        return $this->getConfig()->mixins;
    }

    /**
     * Add a decorator
     *
     * @param mixed $decorator An object implementing ObjectDecoratorInterface, an ObjectIdentifier or an identifier string
     * @param array $config    An array of configuration options
     * @return KObjectIdentifierInterface
     * @see KObject::decorate()
     */
    public function addDecorator($decorator, $config = array())
    {
        if ($decorator instanceof KObjectDecoratorInterface || $decorator instanceof KObjectIdentifier) {
            $this->getDecorators()->append(array($decorator));
        } else {
            $this->getDecorators()->append(array($decorator => $config));
        }

        return $this;
    }

    /**
     * Get the decorators
     *
     *  @return KObjectConfig
     */
    public function getDecorators()
    {
        if(!isset($this->getConfig()->decorators)) {
            $this->getConfig()->append(array('decorators' => array()));
        }

        return $this->getConfig()->decorators;
    }

    /**
     * Formats the identifier as a type:[//domain/]package.[.path].name string
     *
     * @return string
     */
    public function toString()
    {
        if($this->_identifier == '')
        {
            if(!empty($this->_type)) {
                $this->_identifier .= $this->_type;
            }

            if(!empty($this->_domain)) {
                $this->_identifier .= '://'.$this->_domain.'/';
            } else {
                $this->_identifier .= ':';
            }

            if(!empty($this->_package)) {
                $this->_identifier .= $this->_package;
            }

            if(count($this->_path)) {
                $this->_identifier .= '.'.implode('.',$this->_path);
            }

            if(!empty($this->_name)) {
                $this->_identifier .= '.'.$this->_name;
            }
        }

        return $this->_identifier;
    }

    /**
     * Formats the identifier as an associative array
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'domain'      => $this->_domain,
            'type'		  => $this->_type,
            'package'	  => $this->_package,
            'path'		  => $this->_path,
            'name'		  => $this->_name,
        );

        return $data;
    }

    /**
     *Implements access to virtual properties so that it appears to be a read-only public property.
     *
     * @param   string  $property The virtual property to return.
     * @return  array   The value of the virtual property.
     */
    public function __get($property)
    {
        $result = null;
        if(isset($this->{'_'.$property})) {
            $result = $this->{'_'.$property};
        }

        return $result;
    }

    /**
     * This function checks if a virtual property is set.
     *
     * @param   string  $property The virtual property to return.
     * @return  boolean True if it exists otherwise false.
     */
    public function __isset($property)
    {
        $name = ltrim($property, '_');
        $vars = get_object_vars($this);

        return isset($vars['_'.$name]);
    }

    /**
     * Allow casting of the identifier to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Prevent creating clones of this class
     *
     * @throws Exception
     */
    final private function __clone()
    {
        trigger_error("An object identifier is an immutable object and cannot be cloned.", E_USER_WARNING);
    }
}
