<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollection;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\Resource\HttpResourceInterface;
use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;
use Totallywicked\DevTest\Model\AbstractModel;
use Totallywicked\DevTest\Model\ResourceIterator;
use Totallywicked\DevTest\Factory\FactoryInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use Totallywicked\DevTest\Model\Resource\CharacterResource;
use Totallywicked\DevTest\Model\Resource\LocationResource;
use Totallywicked\DevTest\Model\Resource\EpisodeResource;
use Totallywicked\DevTest\Model\Character;
use Totallywicked\DevTest\Model\Location;
use Totallywicked\DevTest\Model\Episode;
use Totallywicked\DevTest\Service\SearchAggregator;

/**
 * Test for the SearchAggregator
 */
final class SearchAggregatorTest extends TestCase
{
    private static $data;
    private $search;
    public $characterResource;
    public $locationResource;
    public $episodeResource;

    /**
     * @regression
     * @covers SearchAggregator
     * @testdox Calling the SearchAggregator returns correct results.
     * @testWith [{"name": "Morty"}, 1, 10, 5, 42]
     *           [{"name": "Morty"}, 2, 10, 7, 118]
     *           [{"name": "Morty"}, 1, 20, 13, 84]
     */
    public function testSearch($query, $page, $pageSize, $testIndex, $testId)
    {
        $result = $this->search->search($query, $page, $pageSize);

        // Is the right size returned?
        $this->assertCount($pageSize, $result['result']);

        // Check if all items are of the right type
        foreach ($result['result'] as $item) {
            switch ($item['type']) {
                case 'character':
                    $this->assertInstanceOf(Character::class, $item['data']);
                    break;
                case 'location':
                    $this->assertInstanceOf(Location::class, $item['data']);
                    break;
                case 'episode':
                    $this->assertInstanceOf(Episode::class, $item['data']);
                    break;
                default:
                    $this->assertTrue(false, "Should not be reached");
                    break;
            }
        }

        // Check if one the item id's to make sure that the right items are returned.
        $this->assertEquals($testId, $result['result'][$testIndex]['data']['id']);
    }

    /**
     * Read the data from the test data
     */
    public static function setUpBeforeClass(): void
    {
        self::$data = json_decode(file_get_contents(__DIR__ . '/SearchAggregatorTest.json'), TRUE);
    }

    /**
     * Setup a fresh search aggregator for each test
     */
    protected function setUp(): void
    {
        $self = $this;
        $uriFactory = new UriFactory();
        $httpClient = $this->createHttpClient();
        $iteratorFactory = $this->createIteratorFactory();
        $collectionFactory = $this->createCollectionFactory($iteratorFactory);

        $characterFactory = $this->createModelFactory(Character::class, function($args) use ($self)
        {
            $data = isset($args['data']) ? $args['data'] : [];
            return [$self->episodeResource, $self->locationResource, $data];
        });
        $locationFactory = $this->createModelFactory(Location::class, function($args) use ($self)
        {
            $data = isset($args['data']) ? $args['data'] : [];
            return [$self->characterResource, $data];
        });
        $episodeFactory = $this->createModelFactory(Episode::class, function($args) use ($self)
        {
            $data = isset($args['data']) ? $args['data'] : [];
            return [$self->characterResource, $data];
        });
        
        $this->characterResource = new CharacterResource(
            $httpClient,
            $iteratorFactory,
            $characterFactory,
            $collectionFactory,
            $uriFactory->createUri('https://rickandmortyapi.com/api/character')
        );
        $this->locationResource = new LocationResource(
            $httpClient,
            $iteratorFactory,
            $locationFactory,
            $collectionFactory,
            $uriFactory->createUri('https://rickandmortyapi.com/api/location')
        );
        $this->episodeResource = new EpisodeResource(
            $httpClient,
            $iteratorFactory,
            $episodeFactory,
            $collectionFactory,
            $uriFactory->createUri('https://rickandmortyapi.com/api/episode')
        );

        $this->search = new SearchAggregator(
            $this->characterResource,
            $this->locationResource,
            $this->episodeResource
        );
    }

    /**
     * Creates a mocked http client that returns predefined responses
     * @return object
     */
    protected function createHttpClient()
    {
        // Request URL to data map
        $map = self::$data;
        $callback = function ($uri) use ($map) {
            foreach ($map as $key => $value) {
                if ($key === (string) $uri) {
                    return $this->createMockedHttpResponse($value['code'], json_encode($value['content']));
                }
            }
            return null;
        };
        $clientMock = $this->createMock(\GuzzleHttp\Client::class);
        $clientMock->method('get')->will($this->returnCallback($callback));
        return $clientMock;
    }

    /**
     * Return a new mocked iterator factory.
     * @return FactoryInterface
     */
    protected function createIteratorFactory()
    {
        return $this->createMockedFactory(
            $this->getMockBuilder(ResourceIterator::class)
                ->enableOriginalConstructor()
                ->enableOriginalClone()
                ->disableArgumentCloning()
                ->disableAutoReturnValueGeneration(),
            ['resource'],
            true
        );
    }

    /**
     * Return a new mocked model factory.
     * @param string $className
     * @return FactoryInterface
     */
    protected function createModelFactory($className, $args)
    {
        return $this->createMockedFactory(
            $this->getMockBuilder($className)
                ->enableOriginalConstructor()
                ->enableOriginalClone()
                ->disableArgumentCloning()
                ->disableAutoReturnValueGeneration(),
            $args,
            true,
            $className . "Factory"
        );
    }

    /**
     * Return a new mocked collection factory.
     * @param FactoryInterface $iteratorFactory
     * @return FactoryInterface
     */
    protected function createCollectionFactory(FactoryInterface $iteratorFactory)
    {
        return $this->createMockedFactory(
            $this->getMockBuilder(HttpPaginatedCollection::class)
                ->enableOriginalConstructor()
                ->enableOriginalClone()
                ->disableArgumentCloning()
                ->disableAutoReturnValueGeneration(),
            ['resource', $iteratorFactory, 'query'],
            true
        );
    }

    /**
     * Creates and returns a HTTP response mock object
     * @param int $responseCode
     * @param string $responseBody
     * @return object
     */
    protected function createMockedHttpResponse($responseCode, $responseBody)
    {
        $responseMock = $this->createMock(ResponseInterface::class);
        $dataStreamMock = $this->createMock(StreamInterface::class);
        $dataStreamMock->method('getContents')->willReturn($responseBody);
        $responseMock->method('getStatusCode')->willReturn($responseCode);
        $responseMock->method('getBody')->willReturn($dataStreamMock);
        return $responseMock;
    }

    /**
     * Creates and returns mocked factory that manufactures mocked objects
     * @param MockBuilder
     * @return FactoryInterface
     */
    protected function createMockedFactory(
        $mockBuilder,
        $argsToIndex = [],
        $isAbstract = false,
        $factoryClass = FactoryInterface::class
    ) {
        $mock = $this->getMockBuilder($factoryClass)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock->method('make')->will($this->returnCallback(function($factoryArgs)
            use ($mockBuilder, $argsToIndex, $isAbstract)
        {
            $args = [];
            if (is_callable($argsToIndex)) {
                $args = $argsToIndex($factoryArgs);
            } else {
                foreach ($argsToIndex as $key) {
                    if (is_string($key) && isset($factoryArgs[$key])) {
                        $args[] = $factoryArgs[$key];
                    } elseif (!is_string($key)) {
                        $args[] = $key;
                    } else {
                        $args[] = null;
                    }
                }
            }
            if ($isAbstract) {
                return $mockBuilder->setConstructorArgs($args)->getMockForAbstractClass();
            }
            return $mockBuilder->setConstructorArgs($args)->getMock();
        }));
        return $mock;
    }
}
