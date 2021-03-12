<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\Resource\Collection\AbstractHttpPaginatedCollection;

final class HttpPaginatedCollectionTest extends TestCase
{
    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getPage with a number returns that page from the resource.
     * @testWith [1]
     *           [404]
     */
    public function testGetPage($id)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getNumberOfPages returns a number of pages in the resource.
     */
    public function testGetNumberOfPages()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Calling getNumberOfItems returns the size of the resource.
     */
    public function testGetNumberOfItems()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
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
     * @covers AbstractHttpPaginatedCollection
     * @testdox Collection can be iterated over with foreach.
     */
    public function testAccessInterator()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractHttpPaginatedCollection
     * @testdox Collection returns the size of the resource when used with the count() function.
     */
    public function testCount()
    {
        // TODO: Implement
    }
}
