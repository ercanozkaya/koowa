<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Whitespace Template Filter
 *
 * Filter which removes all spaces from the template output
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class TemplateFilterWhitespace extends KTemplateFilterAbstract implements KTemplateFilterRenderer
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_LOWEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Remove all spaces from the template output
     *
     * @param string $text  The text to parse
     * @return void
     */
    public function render(&$text)
    {
        $text = trim(preg_replace('/>\s+</', '><', $text));
    }
}