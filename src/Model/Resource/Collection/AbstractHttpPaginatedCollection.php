<?php
namespace Totallywicked\DevTest\Model\Resource\Collection;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;
use Totallywicked\DevTest\Factory\FactoryInterface;
use \Traversable;

/**
 * Abstract implementation of the HttpPaginatedCollectionInterface
 */
abstract class AbstractHttpPaginatedCollection implements HttpPaginatedCollectionInterface
{
    /**
     * ResourceModel used internally by this collection
     * Use AbstractHttpResource because of fetchQuery dependency.
     * @var AbstractHttpResource
     */
    protected $resource;

    /**
     * Set to the collection factory for this resource.
     * @var FactoryInterface
     */
    protected $iteratorFactory;

    /**
     * The query filters behind this collection,
     * this could be empty in which case no filters are applied.
     * @var array
     */
    protected $query;

    /**
     * Returns a number of pages in this collection
     * @var array
     */
    protected $countPages;

    /**
     * Returns a number of items in this collection
     * @var array
     */
    protected $countItems;

    /**
     * Stores the size of a single page
     * Hardcoded to 10 for performance reasons and because it is valid for rickandmortyapi
     * @var array
     */
    protected $pageSize = 10;

    /**
     * Constructor
     * @param AbstractHttpResource $resource
     * @param FactoryInterface $iteratorFactory
     * @param array $query
     */
    public function __construct(
        AbstractHttpResource $resource,
        FactoryInterface $iteratorFactory,
        array $query = []
    ) {
        // Remove page from query, we manage it separately.
        $this->query = array_merge([], $query, ['page' => null]);
        $this->iteratorFactory = $iteratorFactory;
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    function getPage(int $page): array
    {
        $query = array_merge([], $this->query, ['page' => $page]);
        $result = $this->fetch($query);
        return $result['results'];
    }

    /**
     * @inheritDoc
     */
    function getNumberOfPages(): int
    {
        if ($this->countPages === null) {
            $this->fetch($this->query);
        }
        return $this->countPages;
    }

    /**
     * @inheritDoc
     */
    function getNumberOfItems(): int
    {
        if ($this->countItems === null) {
            $this->fetch($this->query);
        }
        return $this->countItems;
    }

    /**
     * Fetches the query from the resource.
     * @param array $query
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     * @return array
     */
    protected function fetch(array $query): array
    {
        $result = $this->resource->fetchQuery($query);
        $this->countPages = $result['info']['pages'];
        $this->countItems = $result['info']['count'];
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        try {
            return $this->getNumberOfPages();
        } catch (\Throwable $th) {
            return 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->iteratorFactory->make(['resource' => $this]);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        try {
            $max = $this->getNumberOfPages();
            return $offset > 0 && $offset <= $max;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset)
    {
        try {
            return $this->getPage($offset);
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value)
    {
        // Nada
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset)
    {
        // Nada
    }
}
