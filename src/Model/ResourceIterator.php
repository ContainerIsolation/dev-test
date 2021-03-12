<?php
namespace Totallywicked\DevTest\Model;

/**
 * Iterator for resource models
 */
class ResourceIterator implements \Iterator
{
    /**
     * @var \ArrayAccess
     */
    protected $resource;

    /**
     * @var int
     */
    protected $startIndex;

    /**
     * @var int
     */
    protected $index;

    /**
     * @param \ArrayAccess $resource
     * @param \ArrayAccess $resource
     */
    public function __construct(\ArrayAccess $resource, int $startIndex = 1) {
        $this->resource = $resource;
        $this->startIndex = $startIndex;
        $this->index = $startIndex;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->resource[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->index = $this->startIndex;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->current() !== null;
    }
}
