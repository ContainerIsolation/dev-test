<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterface;
use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Factory\FactoryInterface;
use Totallywicked\DevTest\Model\ModelInterface;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\ClientInterface;
use \Traversable;

/**
 * Abstract implementation of the HttpResourceInterface.
 * This implementation automatically caches any returned results.
 */
abstract class AbstractHttpResource implements HttpResourceInterface
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * Set to the base URI for this resource
     * @example http://example.com/api/resource
     * @var UriInterface
     */
    protected $resourceUri;

    /**
     * Set to the collection factory for this resource.
     * @var FactoryInterface
     */
    protected $iteratorFactory;

    /**
     * Set to the model factory for this resource.
     * @var FactoryInterface
     */
    protected $modelFactory;

    /**
     * Set to the collection factory for this resource.
     * @var FactoryInterface
     */
    protected $collectionFactory;

    /**
     * Cached data returned from the resource. 
     * @param array[array] - [string => array]
     */
    protected $cache;

    /**
     * Cached queries returned from the resource. 
     * @param array[array] - [string => array]
     */
    protected $cachedSearches;

    /**
     * Number of items in this resource.
     * @param int|null
     */
    protected $count;

    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param FactoryInterface $iteratorFactory
     * @param FactoryInterface $modelFactory
     * @param FactoryInterface $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        FactoryInterface $iteratorFactory,
        FactoryInterface $modelFactory = null,
        FactoryInterface $collectionFactory = null,
        UriInterface $resourceUri = null
    ) {
        $this->httpClient = $httpClient;
        $this->iteratorFactory = $iteratorFactory;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceUri = $resourceUri;
    }

    /**
     * @inheritDoc
     */
    public function getById(string $id): object
    {
        if (isset($this->cache[$id])) {
            return $this->convertToModel($this->cache[$id]);
        }
        $uri = $this->getResourceUriWithPath($id);
        $response = $this->fetch($uri);
        return $this->convertToModel($response);
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): array
    {
        // Find which $ids we have cached and which ones we need to fetch from the resource.
        $idsToFetch = [];
        $results = [];
        foreach ($ids as $id) {
            if (isset($this->cache[$id])) {
                $results[$id] = $this->cache[$id];
            } else {
                $idsToFetch[] = $id;
            }
        }
        // Fetch the remaining ids from the resource
        if (count($idsToFetch) > 0) {
            $uri = $this->getResourceUriWithPath(implode(",", $idsToFetch));
            $response = $this->fetch($uri);
            if (is_array($response)) {
                foreach ($response as $entry) {
                    $results[$entry['id']] = $entry;
                }
            } else {
                $results[$response['id']] = $response;
            }
        }
        return $this->convertToModel($results);
    }

    /**
     * Search cannot use cache,
     * but returned results are still available for getById methods.
     * @inheritDoc
     */
    public function search(array $query): HttpPaginatedCollectionInterface
    {
        return $this->collectionFactory->make(['query' => $query, 'resource' => $this]);
    }

    /**
     * Ideally I would not expose this method, but it's needed for collections to work.
     * @param array $query - Map of filters in a format: [string => string, ...]
     * @return array
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     */
    public function fetchQuery(array $query): array
    {
        $uri = $this->getResourceUriWithQuery($query);
        $queryKey = (string) $uri;
        if (!isset($this->cachedSearches[$queryKey])) {
            $response = $this->fetch($uri);
            if (!isset($response['results'])) {
                throw new \Exception("Invalid response returned from the resource");
            }
            $this->cachedSearches[$queryKey] = $response;
        }
        $convertedQueryResult = array_merge([], $this->cachedSearches[$queryKey]);
        $convertedQueryResult['results'] = $this->convertToModel($this->cachedSearches[$queryKey]['results']);
        return $convertedQueryResult;
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfItems(): int
    {
        if ($this->count === null) {
            $this->count = $this->fetchNumberOfItems();
        }
        return $this->count;
    }

    /**
     * Gets the number of items from the resource.
     * @return int The number of items or 0
     * @throws \Exception When we don't know what happened
     */
    protected function fetchNumberOfItems()
    {
        $result = $this->fetch($this->resourceUri);
        if (isset($result['info']['count'])) {
            return $result['info']['count'];
        } else {
            throw new \Exception("Invalid response returned from the resource");
        }
    }

    /**
     * Returns a new URI with the given path appended to the resourceUri.
     * @param string $path
     * @return UriInterface
     */
    protected function getResourceUriWithPath(string $path): UriInterface
    {
        $resourcePath = $this->resourceUri->getPath();
        $resourcePath = implode("/", [rtrim($resourcePath, "/"), ltrim($path, "/")]);
        return $this->resourceUri->withPath($resourcePath);
    }

    /**
     * Returns a new URI with the given query appended to the resourceUri.
     * @param array $query
     * @return UriInterface
     */
    protected function getResourceUriWithQuery(array $query): UriInterface
    {
        $queryString = http_build_query($query);
        return $this->resourceUri->withQuery($queryString);
    }

    /**
     * Internal function for making requests to the resource
     * @param UriInterface|string $url
     * @throws InvalidArgumentException When one of the arguments is invalid
     * @throws NotFoundException When no resources was returned
     * @throws \Exception When we don't know what happened
     * @return null|array
     */
    protected function fetch($url)
    {
        if (!is_a($url, UriInterface::class) && !is_string($url)) {
            throw new InvalidArgumentException('$url must be a string or UriInterface');
        }
        $response = $this->httpClient->get($url);
        if ($response->getStatusCode() === 404) {
            throw new NotFoundException(sprintf("%s not found", (string) $url));
        }
        try {
            $contents = $response->getBody()->getContents();
            $data = json_decode($contents, TRUE, 32, JSON_THROW_ON_ERROR);
        } catch (\Throwable $th) {
            throw new \Exception(sprintf("Could not parse the response: %s", $th->getMessage()));
        }
        $this->cacheResponse($data);
        return $data;
    }

    /**
     * Converts the raw data to model(s).
     * We detect if one or more models was passed by checking for the 'id' key.
     * @param array $data
     * @return array[ModelInterface]|ModelInterface
     */
    protected function convertToModel(array $data)
    {
        // One
        if (isset($data['id'])) {
            return $this->modelFactory->make(['data' => $data]);
        }
        // More
        $result = [];
        $firstId = array_keys($data)[0];
        if (isset($data['results'])) {
            foreach ($data['results'] as $entry) {
                $result[$entry['id']] = $this->modelFactory->make(['data' => $entry]);
            }
        }
        elseif (isset($data[$firstId]) && isset($data[$firstId]['id'])) {
            foreach ($data as $entry) {
                $result[$entry['id']] = $this->modelFactory->make(['data' => $entry]);
            }
        }
        return $result;
    }

    /**
     * Caches the models returned from the resource.
     * @param array $response
     * @return self
     */
    protected function cacheResponse(array $response): self
    {
        // One
        if (isset($response['id'])) {
            $this->cache[$response['id']] = $response;
            return $this;
        }
        // More
        if (isset($response['results'])) {
            foreach ($response['results'] as $entry) {
                $this->cache[$entry['id']] = $entry;
            }
        }
        elseif (isset($response[0]) && isset($response[0]['id'])) {
            foreach ($response as $entry) {
                $this->cache[$entry['id']] = $entry;
            }
        }
        return $this;
    }

    /**
     * Checks if the given ID is cached.
     * Used by AbstractHttpPaginatedCollection
     * @param string $id
     * @return bool
     */
    public function isCached(string $id): bool
    {
        if (isset($this->cache[$id])) {
            return true;
        }
        return false;
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
    public function offsetExists(mixed $offset): bool
    {
        try {
            $model = $this->getById($offset);
            return $model !== null;
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
            $model = $this->getById($offset);
            return $model !== null ? $model : null;
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
