<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\AbstractModel;

final class ModelTest extends TestCase
{
    /**
     * @regression
     * @covers AbstractModel
     * @testdox Getting the data returns the correct data.
     * @testWith [1, "One"]
     *           ["Two", 2]
     *           [null, [1 => "One", "Two" => 2]]
     */
    public function testGetData($key, $expectedValue)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox Setting the data does not throw any errors.
     * @testWith ["Three", 3]
     *           [4, "Four"]
     *           [[5 => "Five", "Six" => 6]]
     */
    public function testSetData($key, $value)
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox The model can be serialized.
     */
    public function testSerialize()
    {
        // TODO: Implement
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox The model can be unserialized.
     */
    public function testUnserialize()
    {
        // TODO: Implement
    }
}
