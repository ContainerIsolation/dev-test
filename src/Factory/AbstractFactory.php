<?php
namespace Totallywicked\DevTest\Factory;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;

/**
 * @inheritDoc
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * The name of the class this factory produces.
     * @var string
     */
    protected $className;

    /**
     * @var \DI\FactoryInterface
     */
    protected $container;

    /**
     * Constructor
     * @param \DI\FactoryInterface $container
     * @param string $className
     */
    public function __construct(
        \DI\FactoryInterface $container,
        $className = null
    ) {
        $this->container = $container;
        if ($className !== null) {
            $this->className = $className;
        }
    }

    /**
     * @inheritDoc
     */
    function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    public function make(array $data = []): object
    {
        return $this->container->make($this->getClassName(), $data);
    }
}
