<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Behavior Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Koowa\Library\Command
 */
interface KBehaviorInterface extends KCommandHandlerInterface, KObjectInterface
{
    /**
     * Get the behavior name
     *
     * @return string
     */
    public function getName();

    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported();
}