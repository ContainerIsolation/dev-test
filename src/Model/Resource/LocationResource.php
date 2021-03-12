<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterfaceFactory;
use Totallywicked\DevTest\Model\ResourceIteratorFactory;
use Totallywicked\DevTest\Model\LocationFactory;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\ClientInterface;

/**
 * Resource for accessing locations.
 */
class LocationResource extends AbstractHttpResource
{
    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param ResourceIteratorFactory $iteratorFactory
     * @param LocationFactory $modelFactory
     * @param HttpPaginatedCollectionInterfaceFactory $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        ResourceIteratorFactory $iteratorFactory,
        LocationFactory $modelFactory,
        HttpPaginatedCollectionInterfaceFactory $collectionFactory = null,
        UriInterface $resourceUri = null
    ) {
        $this->httpClient = $httpClient;
        $this->iteratorFactory = $iteratorFactory;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceUri = $resourceUri;
    }
}
