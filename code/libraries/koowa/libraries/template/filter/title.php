<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Title Template Filter
 *
 * Filter to parse <title></title> tags. Filter will loop over all the title tags. By default only first found none
 * empty tag will be used, other tags are ignored.
 *
 * Subsequent tags should define the content="[append\prepend\replace]" attribute to append to, prepend to or replace
 * the initial tag. The separator, default '-' can either be passed though the filters configuration options or can be
 * defined as an extra attribute.  Eg, <title content="prepend" separator="|">[title]</title>
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterTitle extends KTemplateFilterTag
{
    /**
     * The title separator
     *
     * @var	string
     */
    protected $_separator;

    /**
     * Escape the title
     *
     * @var	boolean
     */
    protected $_escape;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_separator = $config->separator;
        $this->_escape    = $config->escape;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'separator' => '-',
            'escape'    => true,
        ));

        parent::_initialize($config);
    }

    /**
	 * Parse the text for script tags
	 *
	 * @param string $text  The text to parse
	 * @return string
	 */
	protected function _parseTags(&$text)
	{
		$tags  = '';
        $title =  '';

		$matches = array();
        if(preg_match_all('#<title(.*)>(.*)<\/title>#siU', $text, $matches))
		{
            foreach(array_unique($matches[2]) as $key => $match)
            {
                //Set required attributes
                $attribs = array(
                    'content'   => 'default',
                    'separator' => $this->_separator
                );

                $attribs   = array_merge($attribs, $this->parseAttributes( $matches[1][$key]));
                $separator = $attribs['separator'];

                if(!empty($title))
                {
                    switch($attribs['content'])
                    {
                        case 'prepend' : $title = $match.' '.$separator.' '.$title; break;
                        case 'append'  : $title = $title.' '.$separator.' '.$match; break;
                        case 'replace' : $title = $match; break;
                    }
                }
                else $title = $match;
            }

            $text = str_replace($matches[0], '', $text);
            $tags .= $this->_renderTag($attribs, $title);
        }

		return $tags;
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
        unset($attribs['content']);
        unset($attribs['separator']);

        $attribs = $this->buildAttributes($attribs);

        if($this->_escape) {
            $content = $this->getTemplate()->escape($content);
        }

		$html = '<title '.$attribs.'>'.$content.'</title>'."\n";
		return $html;
	}
}