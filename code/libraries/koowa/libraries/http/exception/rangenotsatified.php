<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Method Not Allowed Http Exception
 *
 * The request is out of bounds—that, none of the range values overlap the extent of the resource.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Http
 * @see http://tools.ietf.org/html/rfc2616#section-10.4.17
 */
class KHttpExceptionRangeNotSatisfied extends KHttpExceptionAbstract
{
    protected $code = KHttpResponse::REQUESTED_RANGE_NOT_SATISFIED;
}