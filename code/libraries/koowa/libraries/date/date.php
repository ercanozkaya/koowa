<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Date
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Date
 */
class KDate extends KObject implements KDateInterface
{
    /**
     * Translator object
     *
     * @var KTranslator
     */
    private $__translator;

    /**
     * The date object
     *
     * @var DateTime
     */
    protected $_date;

    /**
     * Constructor.
     *
     * @param  KObjectConfig $config A KObjectConfig object with configuration options
     */
    public function __construct($config)
    {
        parent::__construct($config);

        if (!($config->timezone instanceof DateTimeZone)) {
            $config->timezone = new DateTimeZone($config->timezone);
        }

        //Set the translator
        $this->__translator = $config->translator;

        //Set the date
        $this->_date = new DateTime($config->date, $config->timezone);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'date'       => 'now',
            'timezone'   => 'UTC',
            'translator' => 'lib:translator'
        ));
    }

    /**
     * Returns the date formatted according to given format.
     *
     * @param  string $format The format to use
     * @return string
     */
    public function format($format)
    {
        $format = preg_replace_callback('/(?<!\\\)[DlFM]/', array($this, '_translate'), $format);
        return $this->_date->format($format);
    }

    /**
     * Returns human readable date.
     *
     * @param  string $period The smallest period to use. Default is 'second'.
     * @return string Formatted date.
     */
    public function humanize($period = 'second')
    {
        $translator = $this->getTranslator();

        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10);
        $now     = $this->getObject('lib.date');

        if($now != $this->_date)
        {
            if($now->getTimestamp() > $this->getTimestamp())
            {
                $difference = $now->getTimestamp() - $this->getTimestamp();
                $tense      = 'ago';
            }
            else
            {
                $difference = $this->getTimestamp() - $now->getTimestamp();
                $tense      = 'from now';
            }

            for($i = 0; $difference >= $lengths[$i] && $i < 6; $i++) {
                $difference /= $lengths[$i];
            }

            $difference      = round($difference);
            $period_index    = array_search($period, $periods);
            $omitted_periods = $periods;
            array_splice($omitted_periods, $period_index);

            if(in_array($periods[$i], $omitted_periods))
            {
                $difference = 1;
                $i          = $period_index;
            }

            if($periods[$i] == 'day' && $difference == 1)
            {
                // Since we got 1 by rounding it down and if it's less than 24 hours it would say x hours ago, this
                // is yesterday
                return $tense == 'ago' ? $translator->translate('Yesterday') : $translator->translate('Tomorrow');
            }

            $period        = $periods[$i];
            $period_plural = $period.'s';

            // We do not pass $period or $tense as parameters to replace because some languages use different words
            // for them based on the time difference.
            $result = $translator->choose(
                array("{number} $period $tense", "{number} $period_plural $tense"),
                $difference,
                array('number' => $difference)
            );
        }
        else $result = $translator->translate('Just now');

        return $result;
    }

    /**
     * Alters the timestamp
     *
     * @param string $modify A date/time string
     * @return KDate or FALSE on failure.
     */
    public function modify($modify)
    {
        if($this->_date->modify($modify) === false) {
            return false;
        }

        return $this;
    }

    /**
     * Resets the current time of the DateTime object to a different time.
     *
     * @param integer $year     Year of the date.
     * @param integer $month    Month of the date.
     * @param integer $day      Day of the date.
     * @return KDate or FALSE on failure.
     */
    public function setDate($year, $month, $day)
    {
        if($this->_date->setDate($year, $month, $day) === false) {
            return false;
        }

        return $this;
    }

    /**
     * Resets the current date of the DateTime object to a different date.
     *
     * @param integer $hour     Hour of the time.
     * @param integer $minute   Minute of the time.
     * @param integer $second  Second of the time.
     * @return KDate or FALSE on failure.
     */
    public function setTime($hour, $minute, $second = 0)
    {
        if($this->_date->setTime($hour, $minute, $second) === false) {
            return false;
        }

        return $this;
    }

    /**
     * Sets the date and time based on an Unix timestamp.
     *
     * @param DateTimeZone $timezone A DateTimeZone object representing the desired time zone.
     * @return KDate or FALSE on failure.
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        if($this->_date->setTimezone($timezone) === false) {
            return false;
        }

        return $this;
    }

    /**
     * Return time zone relative to given DateTime
     *
     * @return DateTimeZone Return a DateTimeZone object on success or FALSE on failure.
     */

    public function getTimezone()
    {
        return $this->_date->getTimezone();
    }

    /**
     * Sets the date and time based on an Unix timestamp
     *
     * @param integer $timestamp Unix timestamp representing the date.
     * @return KDate or FALSE on failure.
     */
    public function setTimestamp($timestamp)
    {
        $this->_date = new DateTime("@$timestamp");
        return $this;
    }

    /**
     * Gets the Unix timestamp.
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->_date->format('U');
    }

    /**
     * Returns the timezone offset.
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->_date->getOffset();
    }

    /**
     * Gets the translator object
     *
     * @return  KTranslatorInterface
     */
    public function getTranslator()
    {
        if(!$this->__translator instanceof KTranslatorInterface)
        {
            $this->__translator = KObjectManager::getInstance()->getObject($this->__translator);

            if(!$this->__translator instanceof KTranslatorInterface)
            {
                throw new UnexpectedValueException(
                    'Translator: '.get_class($this->__translator).' does not implement KTranslatorInterface'
                );
            }
        }

        return $this->__translator;
    }

    /**
     * Sets the translator object
     *
     * @param  KTranslatorInterface $translator A translator object
     * @return KDate
     */
    public function setTranslator(KTranslatorInterface $translator)
    {
        $this->__translator = $translator;
        return $this;
    }

    /**
     * Translates day and month names.
     *
     * @param array $matches Matched elements of preg_replace_callback.
     * @return string
     */
    protected function _translate($matches)
    {
        $replacement = '';
        $translator  = $this->getTranslator();

        switch ($matches[0])
        {
            case 'D':
                $replacement = $translator->translate(strtoupper($this->_date->format('D')));
                break;

            case 'l':
                $replacement = $translator->translate(strtoupper($this->_date->format('l')));
                break;

            case 'F':
                $replacement = $translator->translate(strtoupper($this->_date->format('F')));
                break;

            case 'M':
                $replacement = $translator->translate(strtoupper($this->_date->format('F').'_SHORT'));
                break;
        }

        $replacement = preg_replace('/^([0-9])/', '\\\\\\\\\1', $replacement);
        $replacement = preg_replace('/([a-z])/i', '\\\\\1', $replacement);

        return $replacement;
    }
}