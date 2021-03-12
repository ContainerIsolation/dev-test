<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Factory\FactoryInterface;
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
     * @param FactoryInterface $iteratorFactory
     * @param LocationFactory $modelFactory
     * @param FactoryInterface $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        FactoryInterface $iteratorFactory,
        LocationFactory $modelFactory = null,
        FactoryInterface $collectionFactory = null,
        UriInterface $resourceUri = null
    ) {
        $this->httpClient = $httpClient;
        $this->iteratorFactory = $iteratorFactory;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->resourceUri = $resourceUri;
    }
}
