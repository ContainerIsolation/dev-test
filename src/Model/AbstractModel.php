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
    public function offsetExists($offset): bool
    {
        return $this->data[$offset] !== null;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
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
    public function unserialize($serialized)
    {
        $this->data = json_decode($serialized, TRUE);
    }

    /**
     * A hacked together method to extract IDs from urls.
     * @param array $urls
     * @return array
     */
    protected function getIdsFromUrls(array $urls)
    {
        $ids = [];
        foreach ($urls as $url) {
            $matches = [];
            $result = preg_match('/.+\/([0-9]+)$/', $url, $matches);
            if ($result === 0 || $result === false) {
                continue;
            }
            $ids[] = $matches['1'];
        }
        if (count($ids) === 1) {
            return $ids[0];
        }
        return $ids;
    }
}
