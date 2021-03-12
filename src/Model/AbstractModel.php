<?php
namespace Totallywicked\DevTest\Model;

/**
 * Abstract model
 */
abstract class AbstractModel implements ModelInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getData($key = null, $defaultValue = null)
    {
        if ($key === null) {
            return array_merge([], $this->data);
        }
        elseif (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function setData($key = null, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge([], $key);
        }
        else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->data = [];
        }
        else {
            unset($this->data[$key]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->data[$offset] !== null;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return json_encode($this->data);
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $serialized)
    {
        $this->data = json_decode($serialized, TRUE);
    }
}
