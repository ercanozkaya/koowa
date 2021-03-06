<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Traversable Model Entity Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Model
 */
interface KModelEntityComposable
{
    /**
     * Find an entity in the collection based on a needle
     *
     * This functions accepts either a know position or associative array of property/value pairs
     *
     * @param 	string $needle The position or the key to search for
     * @return KModelEntityInterface
     */
    public function find($needle);

    /**
     * Create an entity for this collection
     *
     * This function will either clone the entity object, or create a new instance of the entity object for each entity
     * being inserted. By default the entity will be cloned.
     *
     * @param   array   $properties The entity properties
     * @param   string  $status     The entity status
     * @return  KModelEntityComposite
     */
    public function create(array $properties = array(), $status = null);

    /**
     * Insert an entity into the collection
     *
     * The entity will be stored by it's identity_key if set or otherwise by it's object handle.
     *
     * @param  KObjectHandlable|KModelEntityInterface $entity
     * @return boolean    TRUE on success FALSE on failure
     * @throws InvalidArgumentException if the object doesn't implement KModelEntity
     */
    public function insert(KObjectHandlable $entity);

    /**
     * Removes an entity from the collection
     *
     * The entity will be removed based on it's identity_key if set or otherwise by it's object handle.
     *
     * @param  KObjectHandlable|KModelEntityInterface $entity
     * @return KModelEntityComposite
     * @throws InvalidArgumentException if the object doesn't implement KModelEntityInterface
     */
    public function remove(KObjectHandlable $entity);

    /**
     * Checks if the collection contains a specific entity
     *
     * @param   KObjectHandlable|KModelEntityInterface $entity
     * @return  bool Returns TRUE if the object is in the set, FALSE otherwise
     */
    public function contains(KObjectHandlable $entity);
}