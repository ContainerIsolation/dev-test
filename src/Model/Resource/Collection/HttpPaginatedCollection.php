<?php
namespace Totallywicked\DevTest\Model\Resource\Collection;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\OutOfBoundsException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;
use Totallywicked\DevTest\Model\ModelInterface;
use Totallywicked\DevTest\Factory\FactoryInterface;
use \Traversable;

/**
 * Implementation of the HttpPaginatedCollectionInterface
 */
class HttpPaginatedCollection implements HttpPaginatedCollectionInterface
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
     * Local cache for this collection
     * @var array
     */
    protected $queryItemCache;

    /**
     * Stores the size of a single page
     * Hardcoded to 20 for performance reasons and because it is valid for rickandmortyapi
     * @var array
     */
    protected $pageSize = 20;

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
    function getPage(int $page = 1): array
    {
        // Check bounds
        $max = $this->getNumberOfPages();
        if ($page < 1 || $page > $max) {
            throw new OutOfBoundsException(sprintf("Page is smaller than 1 or bigger than %d", $max));
        }
        $query = array_merge([], $this->query, ['page' => $page]);
        $result = $this->fetch($query);
        return $result['results'];
    }

    /**
     * @inheritDoc
     */
    function getItem(int $index): ModelInterface
    {
        // Check bounds
        $numberOfItems = $this->getNumberOfItems();
        if ($index < 0 || $index >= $numberOfItems) {
            throw new OutOfBoundsException(sprintf("Index is smaller than 0 or bigger than %d", $numberOfItems - 1));
        }
        // Check if we have this item the cache
        if (isset($this->queryItemCache[$index])) {
            return $this->queryItemCache[$index];
        }
        // Find on which page we can find this item
        $page = ceil(($index + 1) / $this->pageSize);
        $this->getPage($page);
        // We should find the result in our cache
        if (isset($this->queryItemCache[$index])) {
            return $this->queryItemCache[$index];
        }
        throw new NotFoundException("Could not find the item at the index: $index");
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
     * Fetches the query from the resource and caches the results.
     * @param array $query
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     * @return array
     */
    protected function fetch(array $query): array
    {
        $page = isset($query['page']) ? $query['page'] : 1;
        $result = $this->resource->fetchQuery($query);
        $this->countPages = $result['info']['pages'];
        $this->countItems = $result['info']['count'];
        if (is_array($result['results'])) {
            $values = array_values($result['results']);
            foreach ($values as $key => $value) {
                $queryIndex = (($page - 1) * $this->pageSize) + $key;
                $this->queryItemCache[$queryIndex] = $value;
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        try {
            return $this->getNumberOfItems();
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
    public function offsetExists($offset): bool
    {
        try {
            $max = $this->getNumberOfItems();
            return $offset > 0 && $offset <= $max;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        try {
            return $this->getItem($offset);
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        // Nada
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        // Nada
    }
}
