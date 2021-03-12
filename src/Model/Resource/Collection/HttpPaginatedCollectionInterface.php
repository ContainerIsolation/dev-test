<?php
namespace Totallywicked\DevTest\Model\Resource\Collection;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;

// OutOfBoundsException - use this maybe?

/**
 * A simple model for accessing paginated resources.
 */
interface HttpPaginatedCollectionInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Requests specific page from the paginated resource.
     * @param int $page
     * @return array
     * @throws InvalidArgumentException When $page is invalid
     * @throws NotFoundException When the requested page does not exist
     * @return \Exception When we don't know what happened
     */
    function getPage(int $page): array;

    /**
     * Returns the number of pages for this resource.
     * @return int
     * @throws NotFoundException When the requested resource does not exist
     * @return \Exception When we don't know what happened
     */
    function getNumberOfPages(): int;

    /**
     * Returns the size of the resource, alias of count() with one exception.
     * This method can throw errors while count() will return 0.
     * @return int
     * @throws NotFoundException When the requested resource does not exist
     * @return \Exception When we don't know what happened
     */
    function getNumberOfItems(): int;
}
