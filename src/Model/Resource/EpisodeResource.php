<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\EpisodeFactory;

/**
 * Resource for accessing episodes.
 */
class EpisodeResource extends AbstractHttpResource
{
    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param FactoryInterface $iteratorFactory
     * @param EpisodeFactory $modelFactory
     * @param FactoryInterface $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        FactoryInterface $iteratorFactory,
        EpisodeFactory $modelFactory = null,
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
