<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Event Publisher
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Event
 */
class ComKoowaEventPublisher extends KEventPublisher
{
    /**
     * Publish an event by calling all listeners that have registered to receive it.
     *
     * Function will avoid a recursive loop when an exception is thrown during even publishing and output a generic
     * exception instead.
     *
     * @param  KException           $exception  The exception to be published.
     * @param  array|Traversable    $attributes An associative array or a Traversable object
     * @param  mixed                $target     The event target
     * @return  KEventException
     */
    public function publishException(Exception $exception, $attributes = array(), $target = null)
    {
        try
        {
            //Make sure we have an event object
            $event = new KEventException('onException', $attributes, $target);
            $event->setException($exception);

            parent::publishEvent($event);
        }
        catch (Exception $exception)
        {
            if (version_compare(JVERSION, '3.0', '>=')) {
                JErrorPage::render($exception);
            } else {
                JError::raiseError($exception->getCode(), $exception->getMessage());
            }
        }

        return $event;
    }
}
