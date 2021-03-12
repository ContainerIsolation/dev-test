<?php
namespace Totallywicked\DevTest\Http\Response;

use Totallywicked\DevTest\Factory\AbstractFactory;

/**
 * Factory for @see ResponseFactory
 */
class ResponseFactory extends AbstractFactory
{
    /**
     * @inheritDoc
     */
    protected $className = \Laminas\Diactoros\Response::class;
}
