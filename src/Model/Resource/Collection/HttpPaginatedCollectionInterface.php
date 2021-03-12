<?php
namespace Totallywicked\DevTest\Model\Resource\Collection;

use Totallywicked\DevTest\Exception\OutOfBoundsException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\ModelInterface;

/**
 * A simple model for accessing paginated resources.
 */
interface HttpPaginatedCollectionInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Requests specific page from the paginated resource.
     * @param int $page
     * @return array[ModelInterface]
     * @throws OutOfBoundsException When $page is invalid
     * @throws NotFoundException When the requested page does not exist
     * @throws \Exception When we don't know what happened
     */
    function getPage(int $page): array;

    /**
     * Returns a specific item by the index from the query.
     * @param int $index
     * @return ModelInterface
     * @throws OutOfBoundsException When $index is invalid
     * @throws NotFoundException When the requested page does not exist
     * @throws \Exception When we don't know what happened
     */
    function getItem(int $index): ModelInterface;

    /**
     * Returns the number of pages for this resource.
     * @return int
     * @throws NotFoundException When the requested resource does not exist
     * @throws \Exception When we don't know what happened
     */
    function getNumberOfPages(): int;

    /**
     * Returns the size of the resource, alias of count() with one exception.
     * This method can throw errors while count() will return 0.
     * @return int
     * @throws NotFoundException When the requested resource does not exist
     * @throws \Exception When we don't know what happened
     */
    function getNumberOfItems(): int;
}
