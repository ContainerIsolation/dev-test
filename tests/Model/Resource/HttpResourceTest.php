<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\Resource\AbstractHttpResource;

final class HttpResourceTest extends TestCase
{
    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getById returns the resource with that id.
     * @testWith [1]
     *           [404]
     */
    public function testGetById($id)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getById with multiple ids returns resources with these ids.
     * @testWith [1, 2]
     *           [1, 404]
     */
    public function testGetByIds($ids)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling search returns resources based on that filter.
     * @testWith ['name' => 'Morty']
     *           ['gender' => 'Male']
     */
    public function testSearch()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Calling getNumberOfItems returns the size of the resource.
     */
    public function testGetNumberOfItems()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Accessing a specific index of the resource returns that resource or null
     * @testWith [1]
     *           [404]
     */
    public function testAccessByIndex($index)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Collection can be iterated over with foreach.
     */
    public function testAccessInterator()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpResource
     * @testdox Collection returns the size of the resource when used with the count() function.
     */
    public function testCount()
    {
        // TODO: Implement
    }
}
