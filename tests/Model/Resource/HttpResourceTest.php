<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\Resource\Collection\HttpPaginatedCollectionInterface;
use Totallywicked\DevTest\Model\Resource\HttpResourceInterface;
use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;
use Totallywicked\DevTest\Model\AbstractModel;
use Totallywicked\DevTest\Model\ResourceIterator;
use Totallywicked\DevTest\Factory\FactoryInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Testing AbstractHttpResource against HttpResourceInterface
 */
final class HttpResourceTest extends TestCase
{
    private static $data;
    private $uriCallCount;
    private $resource;

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getById returns the resource with that id.
     * @testWith [1, false]
     *           [404, true]
     */
    public function testGetById($id, $shouldThrow)
    {
        if ($shouldThrow) {
            $this->expectException(NotFoundException::class);
        }
        $model = $this->resource->getById($id);
        $this->assertInstanceOf(AbstractModel::class, $model);
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getById with multiple ids returns resources with these ids.
     * @testWith [[1, 2], [true, true]]
     *           [[1, 404], [true, false]]
     */
    public function testGetByIds($ids, $shouldExist)
    {
        $models = $this->resource->getByIds($ids);
        foreach ($shouldExist as $key => $shouldModelExist) {
            $this->assertEquals($shouldModelExist, isset($models[$ids[$key]]));
            if ($shouldModelExist) {
                $this->assertInstanceOf(AbstractModel::class, $models[$ids[$key]]);
            }
        }
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling search returns HttpPaginatedCollectionInterface.
     * @testWith [{"name": "Morty"}]
     */
    public function testSearch($query)
    {
        $paginatedCollection = $this->resource->search($query);
        $this->assertInstanceOf(HttpPaginatedCollectionInterface::class, $paginatedCollection);
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling fetchQuery returns expected results.
     * @testWith [{"name": "Morty"}]
     */
    public function testFetchQuery($query)
    {
        $results = $this->resource->fetchQuery($query);
        $this->assertIsArray($results);
        $this->assertEquals(54, $results['info']['count']);
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getNumberOfItems returns the size of the resource.
     */
    public function testGetNumberOfItems()
    {
        $numberOfItems = $this->resource->getNumberOfItems();
        $this->assertEquals(671, $numberOfItems);
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getById multiple times with the same id should not make additional calls to the resource.
     * @testWith [[1, 1, 1], 1]
     *           [[1, 1, 2, 2], 2]
     */
    public function testCached($ids, $callCount)
    {
        foreach ($ids as $id) {
            $this->resource->getById($id);
            $this->assertTrue($this->resource->isCached($id));
        }
        $this->assertEquals($callCount, $this->uriCallCount['_total']);
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Accessing a specific index of the resource returns that resource or null
     * @testWith [1, true]
     *           [404, false]
     */
    public function testAccessByIndex($index, $shouldExist)
    {
        $model = $this->resource[$index];
        if ($shouldExist) {
            $this->assertInstanceOf(AbstractModel::class, $model);
        } else {
            $this->assertThat($model, $this->isNull());
        }
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Collection can be iterated over with foreach.
     */
    public function testAccessInterator()
    {
        foreach ($this->resource as $model) {
            $this->assertInstanceOf(AbstractModel::class, $model);
        }
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Collection returns the size of the resource when used with the count() function.
     */
    public function testCount()
    {
        $this->assertEquals(671, count($this->resource));
    }

    /**
     * Read the data from the test data
     */
    public static function setUpBeforeClass(): void
    {
        self::$data = json_decode(file_get_contents(__DIR__ . '/HttpResourceTest.json'), TRUE);
    }

    /**
     * Setup a fresh resource for each test
     */
    protected function setUp(): void
    {
        $uriFactory = new UriFactory();
        $iteratorFactory = $this->createMockedFactory(
            $this->getMockBuilder(ResourceIterator::class)
                ->enableOriginalConstructor()
                ->enableOriginalClone()
                ->disableArgumentCloning()
                ->disableAutoReturnValueGeneration(),
            ['resource'],
            true
        );
        $this->uriCallCount = ["_total" => 0]; // Tracks how many times urls are called.
        $this->resource = $this->getMockForAbstractClass(AbstractHttpResource::class, [
                $this->createMockedHttpClient(),
                $iteratorFactory,
                $this->createMockedFactory(
                    $this->getMockBuilder(AbstractModel::class)
                        ->enableOriginalConstructor()
                        ->enableOriginalClone()
                        ->disableArgumentCloning()
                        ->disableAutoReturnValueGeneration()
                ),
                $this->createMockedFactory(
                    $this->getMockBuilder(HttpPaginatedCollectionInterface::class)
                        ->enableOriginalConstructor()
                        ->enableOriginalClone()
                        ->disableArgumentCloning()
                        ->disableAutoReturnValueGeneration()
                ),
                $uriFactory->createUri('https://rickandmortyapi.com/api/character')
            ]);
    }

    /**
     * Creates a mocked http client that returns predefined responses
     * @return object
     */
    protected function createMockedHttpClient()
    {
        // Request URL to data map
        $map = self::$data;
        $test = $this;
        $callback = function ($uri) use ($map, $test) {
            $test->logUrlCall($uri);
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
    protected function createMockedFactory($mockBuilder, $argsToIndex = [], $isAbstract = false)
    {
        $mock = $this->getMockBuilder(FactoryInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock->method('make')->will($this->returnCallback(function($factoryArgs)
            use ($mockBuilder, $argsToIndex, $isAbstract)
        {
            $args = [];
            foreach ($argsToIndex as $key) {
                if (is_string($key) && isset($factoryArgs[$key])) {
                    $args[] = $factoryArgs[$key];
                } elseif (!is_string($key)) {
                    $args[] = $key;
                } else {
                    $args[] = null;
                }
            }
            if ($isAbstract) {
                return $mockBuilder->setConstructorArgs($args)->getMockForAbstractClass();
            }
            return $mockBuilder->setConstructorArgs($args)->getMock();
        }));
        return $mock;
    }

    /**
     * Increments the URL call counter
     * @param string|
     */
    public function logUrlCall($uri)
    {
        $uri = (string) $uri;
        if (!isset($this->uriCallCount[$uri])) {
            $this->uriCallCount[$uri] = 1;
        } else {
            $this->uriCallCount[$uri]++;
        }
        $this->uriCallCount["_total"]++;
    }
}
