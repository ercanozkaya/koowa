<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Url Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterUrl extends KFilterAbstract implements KFilterTraversable
{
	/**
	 * Validate a value
	 *
	 * @param	mixed	$value Value to be validated
	 * @return	bool	True when the variable is valid
	 */
	public function validate($value)
	{
		$value = trim($value);
		return (false !== filter_var($value, FILTER_VALIDATE_URL));
	}

	/**
	 * Sanitize a value
	 *
	 * @param	mixed	$value Value to be sanitized
	 * @return	string
	 */
	public function sanitize($value)
	{
		return filter_var($value, FILTER_SANITIZE_URL);
	}
}

