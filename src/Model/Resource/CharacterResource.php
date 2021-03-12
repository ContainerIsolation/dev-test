<?php
namespace Totallywicked\DevTest\Model\Resource;

use Totallywicked\DevTest\Model\CharacterFactory;

/**
 * Resource for accessing characters.
 */
class CharacterResource extends AbstractHttpResource
{
    /**
     * Constructor
     * @param ClientInterface $httpClient
     * @param FactoryInterface $iteratorFactory
     * @param CharacterFactory $modelFactory
     * @param FactoryInterface $collectionFactory
     * @param UriInterface $resourceUri
     */
    public function __construct(
        ClientInterface $httpClient,
        FactoryInterface $iteratorFactory,
        CharacterFactory $modelFactory = null,
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
