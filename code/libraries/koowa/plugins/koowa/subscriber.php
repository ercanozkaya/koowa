<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Subscriber Plugin
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Plugin\Koowa
 */
abstract class PlgKoowaSubscriber extends PlgKoowaAbstract implements KEventSubscriberInterface
{
    /**
     * List of event listeners
     *
     * @var array
     */
    private $__listeners;

    /**
     * Constructor.
     *
     * @param   object  $dispatcher Event dispatcher
     * @param   array   $config     Configuration options
     */
    public function __construct($dispatcher, $config = array())
    {
        //Change the dispatcher to the koowa dispatcher
        $dispatcher = $this->getObject('event.publisher');

        parent::__construct($dispatcher, $config);
    }

    /**
     * Connect the plugin to the dispatcher
     *
     * @param $dispatcher
     */
    public function connect($dispatcher)
    {
        //Self attach the plugin to the joomla event publisher
        if($dispatcher instanceof KEventPublisherInterface) {
            $this->subscribe($dispatcher);
        }
    }

    /**
     * Attach one or more listeners
     *
     * Event listeners always start with 'on' and need to be public methods.
     *
     * @param KEventPublisherInterface $publisher
     * @param  integer                 $priority   The event priority, usually between 1 (high priority) and 5 (lowest),
     *                                 default is 3 (normal)
     * @return array An array of public methods that have been attached
     */
    public function subscribe(KEventPublisherInterface $publisher, $priority = KEvent::PRIORITY_NORMAL)
    {
        $handle = $publisher->getHandle();

        if(!$this->isSubscribed($publisher));
        {
            //Get all the public methods
            $reflection = new \ReflectionClass($this);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
            {
                if(substr($method->name, 0, 2) == 'on')
                {
                    $publisher->addListener($method->name, array($this, $method->name), $priority);
                    $this->__listeners[$handle][] = $method->name;
                }
            }
        }

        return $this->__listeners;
    }

    /**
     * Detach all previously attached listeners for the specific dispatcher
     *
     * @param KEventPublisherInterface $publisher
     * @return void
     */
    public function unsubscribe(KEventPublisherInterface $publisher)
    {
        $handle = $publisher->getHandle();

        if($this->isSubscribed($publisher));
        {
            foreach ($this->__listeners[$handle] as $index => $listener)
            {
                $publisher->removeListener($listener, array($this, $listener));
                unset($this->__listeners[$handle][$index]);
            }
        }
    }

    /**
     * Check if the subscriber is already subscribed to the dispatcher
     *
     * @param  KEventPublisherInterface $dispatcher  The event dispatcher
     * @return boolean TRUE if the subscriber is already subscribed to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(KEventPublisherInterface $publisher)
    {
        $handle = $publisher->getHandle();
        return isset($this->__listeners[$handle]);
    }
}
