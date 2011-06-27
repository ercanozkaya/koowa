<?php
/**
 * @version     $Id$
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Template
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateDefault extends KTemplateDefault
{ 
	/**
	 * The cache object
	 *
	 * @var	JCache
	 */
    protected $_cache;
	
	/**
	 * Constructor
	 *
	 * Prevent creating instances of this class by making the contructor private
	 * 
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		if(KFactory::get('lib.joomla.config')->getValue('config.caching')) {
	        $this->_cache = KFactory::tmp('lib.joomla.cache', array('template', 'output'));
		}
	}
	
	
	/**
	 * Load a template by path -- first look in the templates folder for an override
	 * 
	 * This function tries to get the template from the cache. If it cannot be found 
	 * the template file will be loaded from the file system.
	 *
	 * @param   string 	The template path
	 * @param	array	An associative array of data to be extracted in local template scope
	 * @return KTemplateAbstract
	 */
	public function loadFile($path, $data = array(), $process = true)
	{
	    if(isset($this->_cache))
	    {
	        $identifier = md5($path); 
	     
	        if ($template = $this->_cache->get($identifier)) 
	        {
		        // store the path
		        $this->_path = $path;
		    
	            $this->loadString($template, $data, $process);
	            return $this;
	        }
	    } 
	 
		return parent::loadFile($path, $data, $process);;
	}
	
	/**
	 * Searches for the file
	 * 
	 * This function first tries to find a template override, if no override exists
	 * it will try to find the default template
	 *
	 * @param	string	The file path to look for.
	 * @return	mixed	The full path and file name for the target file, or FALSE
	 * 					if the file is not found
	 */
	public function findFile($path)
	{
	    $template  = KFactory::get('lib.joomla.application')->getTemplate();
        $override  = JPATH_THEMES.'/'.$template.'/html';
	    $override .= str_replace(array(JPATH_BASE.'/components', '/views'), '', $path);
	     
	    //Try to load the template override
	    $result = parent::findFile($override);
	    
	    if($result === false) 
	    {
	        //If the path doesn't contain the /tmpl/ folder add it
	        if(strpos($path, '/tmpl/') === false) {
	            $path = dirname($path).'/tmpl/'.basename($path);
	        }
	      
	        $result = parent::findFile($path);
	    } 
	    
	    return $result;
	}

	/**
	 * Parse the template
	 * 
	 * This function implements a caching mechanism when reading the template. If
	 * the tempplate cannot be found in the cache it will be filtered and stored in
	 * the cache. Otherwise it will be loaded from the cache and returned directly.
	 *
	 * @return string	The filtered data
	 */
	public function parse()
	{	
	    if(isset($this->_cache))
	    {
	        $identifier = md5($this->_path);
	     
	        if (!$template = $this->_cache->get($identifier)) 
	        {
	            $template = parent::parse();
	            
	            //Store the object in the cache
		   	    $this->_cache->store($template, $identifier);
	        }
	        
	        return $template;
	    }
	    
	    return parent::parse();
	}
    
    /**
     * Load a template helper
     * 
     * This function merges the elements of the attached view model state with the parameters passed to the helper
     * so that the values of one are appended to the end of the previous one. 
     * 
     * If the view state have the same string keys, then the parameter value for that key will overwrite the state.
     *
     * @param   string  Name of the helper, dot separated including the helper function to call
     * @param   mixed   Parameters to be passed to the helper
     * @return  string  Helper output
     */
    public function renderHelper($identifier, $params = array())
    {
        $view = $this->getView();
        
        if(KInflector::isPlural($view->getName())) 
        {
            if($state = $view->getModel()->getState()) {
                $params = array_merge( $state->getData(), $params);
            }
        } 
        else 
        {
            if($item = $view->getModel()->getItem()) {
                $params = array_merge( $item->getData(), $params);
            }
        }   
        
        return parent::renderHelper($identifier, $params);
    }
}