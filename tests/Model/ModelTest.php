<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Model\AbstractModel;

final class ModelTest extends TestCase
{
    /**
     * @regression
     * @covers AbstractModel
     * @testdox Getting the data returns the correct data.
     * @testWith [{"one": 1, "two": 2, "three": 3}, "one", 1]
     *           [{"one": 1, "two": 2, "three": 3}, null, {"one": 1, "two": 2, "three": 3}]
     *           [{"one": 1, "two": 2, "three": 3}, "four", null]
     */
    public function testGetData($data, $key, $value)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, [$data]);
        $this->assertEquals($value, $model->getData($key));
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox Setting the data does not throw any errors.
     * @testWith [{"one": 1, "two": 2, "three": 3}, "four", 4, "four", 4]
     *           [{"one": 1, "two": 2, "three": 3}, {"four": 4}, null, "four", 4]
     */
    public function testSetData($data, $setKey, $setValue, $expectKey, $expectValue)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, [$data]);
        $model->setData($setKey, $setValue);
        $this->assertEquals($expectValue, $model->getData($expectKey));
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox Unsetting the data causes the key to return null
     * @testWith [{"one": 1, "two": 2, "three": 3}, "one", null]
     *           [{"one": 1, "two": 2, "three": 3}, "two", null]
     */
    public function testUnsetData($data, $key, $value)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, [$data]);
        $model->unsetData($key);
        $this->assertEquals($value, $model->getData($key));
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox Unsetting the data causes the key to return null
     * @testWith [{"one": 1, "two": 2, "three": 3}, "one", 1]
     *           [{"one": 1, "two": 2, "three": 3}, "two", 2]
     */
    public function testArrayAccess($data, $key, $value)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, [$data]);
        $this->assertEquals($value, $model[$key]);
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox The model can be serialized.
     * @testWith [{"one": 1, "two": 2, "three": 3}, "{\"one\":1,\"two\":2,\"three\":3}"]
     */
    public function testSerialize($data, $serializedData)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, [$data]);
        $this->assertEquals($serializedData, $model->serialize());
    }

    /**
     * @regression
     * @covers AbstractModel
     * @testdox The model can be unserialized.
     * @testWith [{"one": 1, "two": 2, "three": 3}, "{\"one\":1,\"two\":2,\"three\":3}"]
     */
    public function testUnserialize($data, $serializedData)
    {
        $model = $this->getMockForAbstractClass(AbstractModel::class, []);
        $model->unserialize($serializedData);
        $this->assertEquals($data, $model->getData());
    }
}
