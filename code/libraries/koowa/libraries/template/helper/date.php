<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Date Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class KTemplateHelperDate extends KTemplateHelperAbstract
{
    /**
     * Returns formatted date according to current local and adds time offset.
     *
     * @param  array  $config An optional array with configuration options.
     * @return string Formatted date.
     */
    public function format($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'date'     => 'now',
            'timezone' => date_default_timezone_get(),
            'format'   => $this->getTemplate()->getFormat() == 'rss' ? DateTime::RSS : $this->translate('DATE_FORMAT_LC1'),
            'default'  => ''
        ));

        $return = $config->default;

        if(!in_array($config->date, array('0000-00-00 00:00:00', '0000-00-00')))
        {
            try
            {
                $date = $this->getObject('lib:date', array('date' => $config->date, 'timezone' => 'UTC'));
                $date->setTimezone(new DateTimeZone($config->timezone));

                $return = $date->format($config->format);
            }
            catch(Exception $e) {}
        }

        return $return;
    }

    /**
     * Returns human readable date.
     *
     * @param  array $config An optional array with configuration options.
     * @return string  Formatted date.
     */
    public function humanize($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'date'      => 'now',
            'timezone'  => date_default_timezone_get(),
            'default'   => $this->translate('Never'),
            'period'    => 'second',

        ));

        $result = $config->default;

        if(!in_array($config->date, array('0000-00-00 00:00:00', '0000-00-00')))
        {
            try
            {
                $date = $this->getObject('date', array('date' => $config->date, 'timezone' => 'UTC'));
                $date->setTimezone(new DateTimeZone($config->timezone));

                $result = $date->humanize($config->period);
            }
            catch(Exception $e) {}
        }
        return $result;
    }
}
