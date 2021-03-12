<?php
namespace Totallywicked\DevTest\Factory;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;

/**
 * A Factory is a special object that allows creating new instances of objects
 * without the use the `new` php keyword.
 * Factories make application more flexible by allowing us to override what objects are
 * being created.
 */
interface FactoryInterface
{
    /**
     * Creates a new object.
     * Data when provided should be an array map where keys
     * reference constructor parameters.
     * @param array|null $data
     * @throws InvalidArgumentException When one of the constructor parameters is invalid
     * @throws NotFoundException When the requested class does not exist
     * @return object
     */
    function make(array $data): object;

    /**
     * Returns the name of the class this factory is creating.
     * @return string
     */
    function getClassName(): string;
}
