<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Translator
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
abstract class ComKoowaTranslatorAbstract extends KTranslatorAbstract
{
    /**
     * A reference to Joomla translator
     *
     * @var object
     */
    protected $_translation_helper;

    /**
     * A prefix attached to every generated key
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Catalogue to map common Joomla keys
     *
     * @var KTranslatorCatalogueInterface
     */
    protected $_alias_catalogue;

    /**
     * Default catalogue that generates the keys
     *
     * @var KTranslatorCatalogueInterface
     */
    protected $_catalogue;

    /**
     * Fallback locale to always load the language files from
     *
     * @var string
     */
    protected $_fallback_locale;

    /**
     * Maps identifier types to words
     *
     * @var array
     */
    protected static $_type_map = array(
        'com' => 'component',
        'mod' => 'module',
        'plg' => 'plugin'
    );

    /**
     * An array of signatures from loaded language files
     * @var array
     */
    protected static $_loaded_files = array();

    /**
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if ($config->fallback_locale) {
            $this->_fallback_locale = $config->fallback_locale;
        }

        $this->setTranslationHelper($config->translation_helper);
        $this->setPrefix($config->prefix);

        $this->setDefaultCatalogue($this->createCatalogue($config->catalogue));
        $this->setAliasCatalogue($this->createCatalogue($config->alias_catalogue));

        if (!in_array('koowa', self::$_loaded_files))
        {
            $this->loadTranslations('com_koowa');
            self::$_loaded_files[] = 'koowa';
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'prefix'     => 'KLS_',
            'catalogue'  => null,
            'alias_catalogue'    => 'aliases',
            'fallback_locale'    => 'en-GB',
            'locale'             => JFactory::getConfig()->get('language'),
            'translation_helper' => JFactory::getLanguage()
        ));

        parent::_initialize($config);
    }

    /**
     * Translates a string and handles parameter replacements
     *
     * @param string $string String to translate
     * @param array  $parameters An array of parameters
     *
     * @return string Translated string
     */
    public function translate($string, array $parameters = array())
    {
        $result = strtolower($string);

        if (empty($result)) {
            $result = '';
        }
        elseif (isset($this->_alias_catalogue[$result])) {
            $result = $this->_translation_helper->_($this->_alias_catalogue[$result]);
        }
        else
        {
            if (substr($string, 0, strlen($this->_prefix)) === $this->_prefix) {
                $key = $string;
            } else {
                $key = $this->getKey($string);
            }

            $result = $this->_translation_helper->_($this->hasKey($key) ? $key : $string);
        }

        return parent::translate($result, $parameters);
    }

    /**
     * Translates a string based on the number parameter passed
     *
     * @param array   $strings    Strings to choose from
     * @param integer $number     The number of items
     * @param array   $parameters An array of parameters
     *
     * @throws InvalidArgumentException
     * @return string Translated string
     */
    public function choose(array $strings, $number, array $parameters = array())
    {
        if (count($strings) < 2) {
            throw new InvalidArgumentException('Choose method requires at least 2 strings to choose from');
        }

        $choice = KTranslatorInflector::getPluralPosition($number, $this->_locale);

        if ($choice === 0) {
            return $this->translate($strings[0], $parameters);
        }

        $key = $this->getKey($strings[1]);
        $found = null;

        while ($choice > 0)
        {
            $looking_for = $key.($choice === 1 ? '' : '_'.$choice);
            if ($this->hasKey($looking_for)) {
                $found = $looking_for;
                break;
            }

            $choice--;
        }

        return $this->translate($found ? $found : $strings[1], $parameters);
    }

    /**
     * Checks if a given string is translatable.
     *
     * @param string $string The string to check.
     * @return bool True if it is, false otherwise.
     */
    public function isTranslatable($string)
    {
        return $this->hasKey($this->getKey($string));
    }

    /**
     * Checks if the translator handles a given translation key.
     *
     * @param string $key The translation key.
     * @return bool True if it does, false otherwise.
     */
    public function hasKey($key)
    {
        return (bool) $this->_translation_helper->hasKey($key);
    }

    /**
     * Gets a key from the catalogue and prefixes it
     *
     * @param string $string Language key
     * @return string Translated string
     */
    public function getKey($string)
    {
        $key = $this->_catalogue->{$string};

        if ($this->_prefix) {
            $key = $this->_prefix.$key;
        }

        return $key;
    }

    /**
     * Load the extension language files.
     *
     * @param string|KObjectIdentifier $extension Extension identifier or name (e.g. com_files)
     * @param string $app Application. Leave blank for current one.
     *
     * @return boolean
     */
    public function loadTranslations($extension, $app = null)
    {
        if ($extension instanceof KObjectIdentifier) {
            $extension = $extension->type.'_'.$extension->package;
        }

        $folder = $this->_getExtensionFolder($extension, $app);

        $results = array();
        $results[] = $this->_loadTranslation($extension, $this->_fallback_locale, $folder);

        if ($this->getLocale() !== $this->_fallback_locale) {
            $results[] = $this->_loadTranslation($extension, $this->getLocale(), $folder);
        }

        return in_array(true, $results);
    }

    /**
     * Gets the folder for an extension
     *
     * @throws BadMethodCallException
     *
     * @param string $extension Extension
     * @param string $app       Application. Leave blank for current one.
     * @return string
     */
    protected function _getExtensionFolder($extension, $app = null)
    {
        $type    = substr($extension, 0, 3);
        $package = substr($extension, 4);

        if ($override = $this->getObject('manager')->getClassLoader()->getLocator('component')->getNamespace(ucfirst($package))) {
            $base = $override;
        }
        else
        {
            switch ($app)
            {
                case 'admin':
                    $base = JPATH_ADMINISTRATOR;
                    break;
                case 'site':
                    $base = JPATH_SITE;
                    break;
                default:
                    $base = JPATH_BASE;
            }
        }

        if (isset(self::$_type_map[$type])) {
            $type_folder = self::$_type_map[$type];
        } else {
            throw new BadMethodCallException(sprintf('Invalid extension type: %s', $type));
        }

        if ($type == 'plg') {
            $parts = explode('_', $package);
            if (count($parts) != 2) throw new BadMethodCallException(sprintf('Invalid plugin: %s', $extension));
            $folder = sprintf('%s/%ss/%s/%s', JPATH_ROOT, $type_folder, $parts[0], $parts[1]);
        } else {
            $folder = sprintf('%s/%ss/%s', $base, $type_folder, $extension);
        }

        return $folder;
    }

    /**
     * Loads a Joomla language file
     *
     * @param string $extension
     * @param string $locale Locale name
     * @param string $base   Base path
     * @return bool
     */
    protected function _loadTranslation($extension, $locale, $base)
    {
        $result    = true;
        $signature = md5($extension.$base.$locale);

        if (!in_array($signature, self::$_loaded_files))
        {
            $result = $this->_translation_helper->load($extension, $base, $locale, true, false);

            if ($result) {
                self::$_loaded_files[] = $signature;
            }
        }

        return $result;
    }

    /**
     * Return the alias catalogue
     *
     * @return KTranslatorCatalogueInterface
     */
    public function getAliasCatalogue()
    {
        return $this->_alias_catalogue;
    }

    /**
     * Set the alias catalogue
     *
     * @param KTranslatorCatalogueInterface $catalogue
     * @return ComKoowaTranslator
     */
    public function setAliasCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_alias_catalogue = $catalogue;
        return $this;
    }

    /**
     * Return the default catalogue
     *
     * @return KTranslatorCatalogueInterface
     */
    public function getDefaultCatalogue()
    {
        return $this->_catalogue;
    }

    /**
     * Set the default catalogue
     *
     * @param KTranslatorCatalogueInterface $catalogue
     * @return ComKoowaTranslator
     */
    public function setDefaultCatalogue(KTranslatorCatalogueInterface $catalogue)
    {
        $this->_catalogue = $catalogue;

        return $this;
    }

    /**
     * Return translation helper
     *
     * @return object
     */
    public function getTranslationHelper()
    {
        return $this->_translation_helper;
    }

    /**
     * Set the translation helper
     *
     * @param object $translator
     * @throws InvalidArgumentException
     * @return ComKoowaTranslator
     */
    public function setTranslationHelper($translator)
    {
        if (!is_object($translator)) {
            throw new InvalidArgumentException('Invalid translator');
        }

        $this->_translation_helper = $translator;
        return $this;
    }

    /**
     * Return the language key prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set the language key prefix
     *
     * @param string $prefix
     * @return ComKoowaTranslator
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }
}
