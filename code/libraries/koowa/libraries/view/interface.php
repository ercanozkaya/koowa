<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * View Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
interface KViewInterface
{
    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   array $data The view data
     * @return  string  The output of the view
     */
    public function render($data = array());

    /**
     * Set a view property
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     * @return  KViewAbstract
     */
    public function set($property, $value);

    /**
     * Get a view property
     *
     * @param   string  $property   The property name.
     * @param  mixed  $default  Default value to return.
     * @return  string  The property value.
     */
    public function get($property, $default = null);

    /**
     * Check if a view property exists
     *
     * @param   string  $property   The property name.
     * @return  boolean TRUE if the property exists, FALSE otherwise
     */
    public function has($property);

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     * @return string Translated string
     */
    public function translate($string, array $parameters = array());

    /**
     * Sets the view data
     *
     * @param   array $data The view data
     * @return  KViewInterface
     */
    public function setData($data);

    /**
     * Get the view data
     *
     * @return  array   The view data
     */
    public function getData();

    /**
	 * Get the name
	 *
	 * @return 	string 	The name of the object
	 */
	public function getName();

    /**
     * Get the title
     *
     * @return 	string 	The title of the view
     */
    public function getTitle();

	/**
	 * Get the format
	 *
	 * @return 	string 	The format of the view
	 */
	public function getFormat();

    /**
     * Get the content
     *
     * @return  string The content of the view
     */
    public function getContent();

    /**
     * Get the content
     *
     * @param  string $content The content of the view
     * @return KViewInterface
     */
    public function setContent($content);

	/**
	 * Get the model object attached to the controller
	 *
	 * @return	KModelInterface
	 */
	public function getModel();

	/**
	 * Method to set a model object attached to the view
	 *
	 * @param	mixed	$model An object that implements KObjectInterface, KObjectIdentifier object
	 * 					       or valid identifier string
	 * @throws	UnexpectedValueException	If the identifier is not a table identifier
	 * @return	KViewInterface
	 */
    public function setModel($model);

    /**
     * Gets the translator object
     *
     * @return  KTranslatorInterface
     */
    public function getTranslator();

    /**
     * Sets the translator object
     *
     * @param string|KTranslatorInterface $translator A translator object or identifier
     * @return KViewInterface
     */
    public function setTranslator($translator);

    /**
     * Get the view url
     *
     * @return  KHttpUrl  A HttpUrl object
     */
    public function getUrl();

    /**
     * Set the view url
     *
     * @param KHttpUrl $url   A KHttpUrl object or a string
     * @return  KViewAbstract
     */
    public function setUrl(KHttpUrl $url);

    /**
     * Get a route based on a full or partial query string
     *
     * 'option', 'view' and 'layout' can be omitted. The following variations will all result in the same route :
     *
     * - foo=bar
     * - option=com_mycomp&view=myview&foo=bar
     *
     * In templates, use @route()
     *
     * @param   string|array $route  The query string or array used to create the route
     * @param   boolean      $fqr    If TRUE create a fully qualified route. Defaults to FALSE.
     * @param   boolean      $escape If TRUE escapes the route for xml compliance. Defaults to TRUE.
     * @return  KHttpUrl     The route
     */
    //public function getRoute($route, $fqr = null, $escape = null);

    /**
     * Get the view context
     *
     * @return  KViewContext
     */
    public function getContext();

    /**
     * Check if we are rendering an entity collection
     *
     * @return bool
     */
    public function isCollection();
}
