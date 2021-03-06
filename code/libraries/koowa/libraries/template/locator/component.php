<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Component Template Locator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateLocatorComponent extends KTemplateLocatorAbstract
{
    /**
     * Locate the template based on a virtual path
     *
     * @param  string $path  Stream path or resource
     * @return string The physical stream path for the template
     * @throws RuntimeException If a partial template path is passed and no base template has been loaded.
     */
    public function locate($path)
    {
        //Qualify partial templates.
        if(strpos($path, ':') === false)
        {
            if(!$base = $this->getTemplate()->getPath()) {
                throw new RuntimeException('Cannot qualify partial template path');
            }

            $identifier = $this->getIdentifier($base)->toArray();

            $format    = pathinfo($path, PATHINFO_EXTENSION);
            $template  = pathinfo($path, PATHINFO_FILENAME);

            $parts     = $identifier['path'];
            array_pop($parts);
        }
        else
        {
            // Need to clone here since we use array_pop and it modifies the cached identifier
            $identifier = $this->getIdentifier($path)->toArray();

            $format    = $identifier['name'];
            $template  = array_pop($identifier['path']);
            $parts     = $identifier['path'];
        }

        if(!empty($identifier['domain'])) {
            $rootpath = $this->getObject('manager')->getClassLoader()->getBasepath($identifier['domain']);
        } else {
            $rootpath  = $this->getObject('manager')->getClassLoader()->getLocator('component')->getNamespace(ucfirst($identifier['package']));
        }

        $basepath  = $rootpath.'/components/com_'.strtolower($identifier['package']);
        $filepath  = 'views/'.implode('/', $parts).'/tmpl';
        $fullpath  = $basepath.'/'.$filepath.'/'.$template.'.'.$format.'.php';

        // Find the template
        $result = $this->realPath($fullpath);

        return $result;
    }
}