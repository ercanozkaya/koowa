<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Url Template Filter
 *
 * Filter allows to create url aliases that are replaced on compile and render. A default media:// alias is
 * added that is rewritten to '/media/'.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterUrl extends KTemplateFilterAbstract implements KTemplateFilterCompiler, KTemplateFilterRenderer
{
    /**
     * The alias map
     *
     * @var array
     */
    protected $_aliases;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        foreach($config->aliases as $alias => $path) {
            $this->addAlias($alias, $path);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'aliases' => array('media://' => '/media/'),
        ));

        parent::_initialize($config);
    }

    /**
     * Add a path alias
     *
     * @param string $alias Alias to be appended
     * @param mixed  $value Value
     * @return KTemplateFilterUrl
     */
    public function addAlias($alias, $value)
    {
        $this->_aliases[$alias] = $value;
        return $this;
    }

    /**
     * Convert the schemas to their real paths
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function compile(&$text)
    {
        $text = str_replace(
            array_keys($this->_aliases),
            array_values($this->_aliases),
            $text);
    }

    /**
     * Convert the schemas to their real paths
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function render(&$text)
    {
        $text = str_replace(
            array_keys($this->_aliases),
            array_values($this->_aliases),
            $text);
    }
}