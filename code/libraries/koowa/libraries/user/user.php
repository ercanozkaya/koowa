<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * User Singleton
 *
 * User is the user implementation used by the in-memory user provider. This object is tightly coupled to the session.
 * all data is stored and retrieved from the session attribute container, using a special 'user' namespace to avoid
 * conflicts.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\User
 */
class KUser extends KUserAbstract implements KObjectSingleton
{
    /**
     * Get the user session
     *
     * This function will create a session object if it hasn't been created yet.
     *
     * @return KUserSessionInterface
     */
    public function getSession()
    {
        return $this->getObject('lib:user.session');
    }

    /**
     * Returns the id of the user
     *
     * @return int The id
     */
    public function getId()
    {
        return $this->getSession()->get('user.id');
    }

    /**
     * Returns the email of the user
     *
     * @return string The email
     */
    public function getEmail()
    {
        return $this->getSession()->get('user.email');
    }

    /**
     * Returns the name of the user
     *
     * @return string The name
     */
    public function getName()
    {
        return $this->getSession()->get('user.name');
    }

    /**
     * Returns the roles of the user
     *
     * @return int The role id
     */
    public function getRoles()
    {
        return $this->getSession()->get('user.roles');
    }

    /**
     * Returns the groups the user is part of
     *
     * @return array An array of group id's
     */
    public function getGroups()
    {
        return $this->getSession()->get('user.groups');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text password will be salted, encoded, and
     * then compared to this value.
     *
     * @return string The password or NULL if no password defined
     */
    public function getPassword()
    {
        return null; //return NULL by default
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt or NULL if no salt defined
     */
    public function getSalt()
    {
        return null; //return NULL by default
    }

    /**
     * Checks whether the user is not logged in
     *
     * @return Boolean true if the user is not logged in, false otherwise
     */
    public function isAuthentic()
    {
        return $this->getSession()->get('user.authentic');
    }

    /**
     * Checks whether the user is enabled.
     *
     * @return Boolean true if the user is not logged in, false otherwise
     */
    public function isEnabled()
    {
        return $this->getSession()->get('user.enabled');
    }

    /**
     * Checks whether the user account has expired.
     *
     * @return Boolean
     */
    public function isExpired()
    {
        return $this->getSession()->get('user.expired');
    }

    /**
     * Get the user data as an array
     *
     * @return array An associative array of data
     */
    public function toArray()
    {
        return $this->getSession()->get('user');
    }

    /**
     * Set the user data from an array
     *
     * @param  array $data An associative array of data
     * @return KUser
     */
    public function setData($data)
    {
        parent::setData($data);

        //Set the user data
        $this->getSession()->set('user', KObjectConfig::unbox($data));

        return $this;
    }

    /**
     * Get an user attribute
     *
     * @param   string  $identifier Attribute identifier, eg .foo.bar
     * @param   mixed   $default    Default value when the attribute doesn't exist
     * @return  mixed   The value
     */
    public function get($identifier, $default = null)
    {
        return $this->getSession()->get('user.attributes'.$identifier, $default);
    }

    /**
     * Set an user attribute
     *
     * @param   mixed   $identifier Attribute identifier, eg foo.bar
     * @param   mixed   $value Attribute value
     * @return KUser
     */
    public function set($identifier, $value)
    {
        $this->getSession()->set('user.attributes'.$identifier, $value);
        return $this;
    }

    /**
     * Check if a user attribute exists
     *
     * @param   string  $identifier Attribute identifier, eg foo.bar
     * @return  boolean
     */
    public function has($identifier)
    {
        return $this->getSession()->has('user.attributes'.$identifier);
    }

    /**
     * Removes an user attribute
     *
     * @param string $identifier Attribute identifier, eg foo.bar
     * @return KUser
     */
    public function remove($identifier)
    {
        $this->getSession()->remove('user.attributes'.$identifier);
        return $this;
    }

    /**
     * Get a user attribute
     *
     * @param   string $name  The attribute name.
     * @return  string $value The attribute value.
     */
    public function __get($name)
    {
        return $this->getSession()->get('user.attributes'.$name);
    }

    /**
     * Set a user attribute
     *
     * @param   string $name  The attribute name.
     * @param   mixed  $value The attribute value.
     * @return  void
     */
    public function __set($name, $value)
    {
        $this->getSession()->set('user.attributes'.$name, $value);
    }

    /**
     * Test existence of a use attribute
     *
     * @param  string $name The attribute name.
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->getSession()->has('user.attributes'.$name);
    }

    /**
     * Unset a user attribute
     *
     * @param   string $key  The attribute name.
     * @return  void
     */
    public function __unset($name)
    {
        $this->getSession()->remove('user.attributes'.$name);
    }
}