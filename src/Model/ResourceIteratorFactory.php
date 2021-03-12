<?php
namespace Totallywicked\DevTest\Model;

use Totallywicked\DevTest\Factory\AbstractFactory;

/**
 * Factory for @see ResourceIterator::class
 */
class ResourceIteratorFactory extends AbstractFactory
{
    /**
     * @inheritDoc
     */
    protected $className = ResourceIterator::class;
}
