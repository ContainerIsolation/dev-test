<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterfaceFactory;
use Totallywicked\DevTest\Model\ResourceIteratorFactory;
use Totallywicked\DevTest\Model\EpisodeFactory;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\ClientInterface;

/**
 * Resource for accessing episodes.
 */
class EpisodeResource extends AbstractHttpResource
{
    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param ResourceIteratorFactory $iteratorFactory
     * @param EpisodeFactory $modelFactory
     * @param HttpPaginatedCollectionInterfaceFactory $collectionFactory
     * @param UriInterface $resourceUri
     * @param int $throttle
     */
    public function __construct(
        ClientInterface $httpClient,
        ResourceIteratorFactory $iteratorFactory,
        EpisodeFactory $modelFactory,
        HttpPaginatedCollectionInterfaceFactory $collectionFactory,
        UriInterface $resourceUri = null,
        int $throttle = 0
    ) {
        parent::__construct(
            $httpClient,
            $iteratorFactory,
            $modelFactory,
            $collectionFactory,
            $resourceUri,
            $throttle
        );
    }
}
