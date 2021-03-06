<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Script Template Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateFilterLink extends KTemplateFilterLink
{
    /**
     * Find any virtual tags and render them
     *
     * This function will pre-pend the tags to the content
     *
     * @param string $text  The text to parse
     */
    public function render(&$text)
    {
        $request = $this->getObject('request');
        $links   = $this->_parseTags($text);

        if($this->getTemplate()->getView()->getLayout() == 'koowa') {
            $text = str_replace('<ktml:link>', $links, $text);
        } else  {
            $text = $links.$text;
        }
    }

    /**
     * Render the tag
     *
     * @param 	array	$attribs Associative array of attributes
     * @param 	string	$content The tag content
     * @return string
     */
    protected function _renderTag($attribs = array(), $content = null)
    {
        if($this->getTemplate()->getView()->getLayout() !== 'koowa')
        {
            $link      = isset($attribs['src']) ? $attribs['src'] : false;
            $relType  = 'rel';
            $relValue = $attribs['rel'];
            unset($attribs['rel']);

            JFactory::getDocument()->addHeadLink($link, $relValue, $relType, $attribs);
        }
        else return parent::_renderTag($attribs, $content);
    }
}
