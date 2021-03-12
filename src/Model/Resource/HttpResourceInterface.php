<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterface;
use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;

/**
 * A simple model for accessing resources behind remote HTTP endpoint.
 * Array access can be used for accessing elements by their ID.
 */
interface HttpResourceInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Look up the resource by its id.
     * @param string $id
     * @return object
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     */
    function getById(string $id): object;

    /**
     * Like getById but can return multiple resources.
     * Non existing ids will not be returned in the array.
     * It is possible to receive empty array, it is important to check both
     * for NotFoundException and empty array.
     * @param array $ids
     * @return array - returns array of arrays
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     */
    function getByIds(array $ids): array;

    /**
     * Allows searching the resource using query filters.
     * Instead of returning an array it returns a collection
     * that can be used to request more resources.
     * @param array $query - Map of filters in a format: [string => string, ...]
     * @return HttpPaginatedCollectionInterface
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     */
    function search(array $query): HttpPaginatedCollectionInterface;

    /**
     * Returns the size of the resource, alias of count() with one exception.
     * This method can throw errors while count() will return 0.
     * @return int
     * @throws NotFoundException When the requested resource does not exist
     * @throws \Exception When we don't know what happened
     */
    function getNumberOfItems(): int;
}
