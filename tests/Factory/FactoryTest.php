<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Factory\AbstractFactory;
use DI\FactoryInterface;

final class FactoryTest extends TestCase
{
    public $passedArguments;

    /**
     * @regression
     * @covers AbstractFactory
     * @testdox Calling make returns a newly created object.
     * @testWith [{"className": "\\Totallywicked\\DevTest\\Template\\TemplateInterface"}]
     *           [{"className": "\\Totallywicked\\DevTest\\Http\\Router\\RouterInterface"}]
     */
    public function testMakeObject($data)
    {
        $object = $this->getFactory($data['className'])->make(['arg1' => 'myarg']);
        $this->assertInstanceOf($data['className'], $object);
        $this->assertEquals('myarg', $this->passedArguments['arg1']);
    }

    /**
     * Setup a factory that uses mocked container for testing purposes.
     * @param string $className
     * @return AbstractFactory
     */
    protected function getFactory($className)
    {
        return $this->getMockForAbstractClass(AbstractFactory::class, [
                $this->createMockedContainer(),
                $className
            ]);
    }

    /**
     * Creates and returns mocked container that manufactures fake objects.
     * @param MockBuilder
     * @return FactoryInterface
     */
    protected function createMockedContainer()
    {
        $self = $this;
        $mock = $this->getMockBuilder(FactoryInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock->method('make')->will($this->returnCallback(function($className, $factoryArgs) use ($self)
        {
            $self->passedArguments = $factoryArgs;
            return $self->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->enableAutoReturnValueGeneration()
                ->getMock();
        }));
        return $mock;
    }
}
