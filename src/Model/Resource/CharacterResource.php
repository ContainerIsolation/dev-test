<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterfaceFactory;
use Totallywicked\DevTest\Model\ResourceIteratorFactory;
use Totallywicked\DevTest\Model\CharacterFactory;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\ClientInterface;

/**
 * Resource for accessing characters.
 */
class CharacterResource extends AbstractHttpResource
{
    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param ResourceIteratorFactory $iteratorFactory
     * @param CharacterFactory $modelFactory
     * @param HttpPaginatedCollectionInterfaceFactory $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        ResourceIteratorFactory $iteratorFactory,
        CharacterFactory $modelFactory,
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
