<?php
use PHPUnit\Framework\TestCase;

use Totallywicked\DevTest\Factory\AbstractFactory;

final class FactoryTest extends TestCase
{
    /**
     * @regression
     * @covers AbstractFactory
     * @testdox Calling make returns a newly created object.
     * @testWith [\Totallywicked\DevTest\Template\TemplateInterface]
     *           [\Totallywicked\DevTest\Http\Router\RouterInterface]
     */
    public function testMakeObject($className)
    {
        // TODO: Implement
    }
}
