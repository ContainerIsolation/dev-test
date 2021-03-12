<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\Resource\Collection\AbstractHttpPaginatedCollection;
use Totallywicked\DevTest\Exception\NotFoundException;
use Totallywicked\DevTest\Model\Resource\HttpResourceInterface;
use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;
use Totallywicked\DevTest\Model\AbstractModel;
use Totallywicked\DevTest\Factory\FactoryInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * This test uses the abstract resource but does not cover it.
 */
final class HttpPaginatedCollectionTest extends TestCase
{
    private static $data;
    private $collection;
    private $resource;

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getPage with a number returns that page from the resource.
     * @testWith [{"name": "Morty"}, 1, 1]
     *           [{"name": "Morty"}, 2, 11]
     */
    public function testGetPage($query, $page, $modelId)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        $result = $collection->getPage($page);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(AbstractModel::class, $result[$modelId]);
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getNumberOfPages returns a number of pages in the resource.
     * @testWith [{"name": "Morty"}, 2]
     */
    public function testGetNumberOfPages($query, $count)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        $result = $collection->getNumberOfPages();
        $this->assertEquals($count, $result);
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getNumberOfItems returns the size of the resource.
     * @testWith [{"name": "Morty"}, 54]
     */
    public function testGetNumberOfItems($query, $count)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        $result = $collection->getNumberOfItems();
        $this->assertEquals($count, $result);
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Accessing a specific index of the resource returns that resource or null
     * @testWith [{"name": "Morty"}, 1, true]
     *           [{"name": "Morty"}, 404, false]
     */
    public function testAccessByIndex($query, $index, $shouldExist)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        $result = $collection[$index];
        if ($shouldExist) {
            $this->assertCount(1, $result);
            $this->assertContainsOnlyInstancesOf(AbstractModel::class, $result);
        } else {
            $this->assertThat($result, $this->isNull());
        }
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Collection can be iterated over with foreach.
     * @testWith [{"name": "Morty"}, [0, 1, 1]]
     */
    public function testAccessInterator($query, $counts)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        foreach ($collection as $i => $page) {
            $this->assertCount($counts[$i], $page);
            $this->assertContainsOnlyInstancesOf(AbstractModel::class, $page);
        }
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Collection returns the size of the resource when used with the count() function.
     * @testWith [{"name": "Morty"}, 2]
     */
    public function testCount($query, $count)
    {
        $collection = $this->resource->search($query);
        $this->assertInstanceOf(AbstractHttpPaginatedCollection::class, $collection);
        $this->assertCount($count, $collection);
    }

    /**
     * Read the data from the test data
     */
    public static function setUpBeforeClass(): void
    {
        self::$data = json_decode(file_get_contents(__DIR__ . '/HttpPaginatedCollectionTest.json'), TRUE);
    }

    /**
     * Setup a fresh collection for each test
     */
    protected function setUp(): void
    {
        $uriFactory = new UriFactory();
        $this->resource = $this->getMockForAbstractClass(AbstractHttpResource::class, [
                $this->createMockedHttpClient(),
                $this->createMockedFactory(
                    $this->getMockBuilder(AbstractModel::class)
                        ->enableOriginalConstructor()
                        ->enableOriginalClone()
                        ->disableArgumentCloning()
                        ->disableAutoReturnValueGeneration()
                ),
                $this->createMockedFactory(
                    $this->getMockBuilder(AbstractHttpPaginatedCollection::class)
                        ->enableOriginalConstructor()
                        ->enableOriginalClone()
                        ->disableArgumentCloning()
                        ->disableAutoReturnValueGeneration(),
                    ['resource', 'query'],
                    true
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
        $callback = function ($uri) use ($map) {
            var_dump((string) $uri);
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
        $mock->method('make')->will($this->returnCallback(function($factoryArgs) use ($mockBuilder, $argsToIndex, $isAbstract)
        {
            $args = [];
            foreach ($argsToIndex as $key) {
                if (isset($factoryArgs[$key])) {
                    $args[] = $factoryArgs[$key];
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
}
