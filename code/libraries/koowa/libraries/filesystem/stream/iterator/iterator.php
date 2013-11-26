<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * FileSystem Stream Iterator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filesystem
 */
class KFilesystemStreamIterator implements SeekableIterator
{
    /**
     * Size of each chunk
     *
     * @var int
     */
    private $__stream;

    /**
     * The current chunk
     *
     * @var string
     */
    protected $_chunk;

    /**
     * Constructor.
     *
     * @param KFilesystemStream $stream  A FilesystemStream object
     */
    public function __construct(KFilesystemStream $stream)
    {
        $this->_chunk      = '';
        $this->__stream    = $stream;
    }

    /**
     * Seeks to a given position in the stream
     *
     * @param int $position
     * @throws OutOfBoundsException If the position is not seekable.
     */
    public function seek($position)
    {
        if ($position > $this->getStream()->getSize()) {
            throw new OutOfBoundsException('Invalid seek position ('.$position.')');
        }

        $this->getStream()->seek($position, SEEK_SET);
    }

    /**
     * Read data from the stream and advance the pointer
     *
     * @return string
     */
    public function current()
    {
        return $this->_chunk;
    }

    /**
     * Returns the current position of the stream read/write pointer
     *
     * @return bool|int|mixed
     */
    public function key()
    {
        return $this->getStream()->peek();
    }

    /**
     * Move to the next chunk
     *
     * @return void
     */
    public function next()
    {
        $this->_chunk = $this->getStream()->read();
    }

    /**
     * Rewind to the beginning of the stream
     *
     * @return void
     */
    public function rewind()
    {
        $this->getStream()->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return !$this->getStream()->eof();
    }

    /**
     * Get the chunk size
     *
     * @return integer
     */
    public function getChunkSize()
    {
        return $this->getStream()->getChunkSize();
    }

    /**
     * Get the stream object
     *
     * @return KFilesystemStream
     */
    public function getStream()
    {
        return $this->__stream;
    }
}