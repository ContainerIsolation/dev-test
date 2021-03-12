<?php
namespace Totallywicked\DevTest\Model\Resource\Collection;

use Totallywicked\DevTest\Factory\AbstractFactory;

/**
 * Factory for @see HttpPaginatedCollectionInterface
 */
class HttpPaginatedCollectionInterfaceFactory extends AbstractFactory
{
    /**
     * @inheritDoc
     */
    protected $className = HttpPaginatedCollectionInterface::class;
}
