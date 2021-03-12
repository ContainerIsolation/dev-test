<?php
namespace Totallywicked\DevTest\Model;

/**
 * A very simple model interface that implements basic get data / set data methods.
 * Proper getters and setters are expected to be implemented inside the actual models.
 */
interface ModelInterface extends \Serializable, \ArrayAccess
{
    /**
     * Return data from the set key or return the default value.
     * If no key is provided we return a copy of everything we have.
     * 
     * @param string|null $key
     * @param mixed $defaultValue
     * @return mixed
     */
    function getData($key = null, $defaultValue = null);

    /**
     * Sets data on the given key.
     * If no value is provided we expect the key to be an array,
     * in such case every element will be copied to this model.
     * 
     * @param string|array $key
     * @param mixed $value
     * @return self
     */
    function setData($key = null, $value = null);

    /**
     * Calls unset on the given key.
     * Calling without $key will unset all values.
     * 
     * @param string $key
     * @return self
     */
    function unsetData($key = null);
}