<?php
namespace Totallywicked\DevTest\Http\Router;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Router interface for this application.
 * Intended as an adapter for external router implementations.
 */
interface RouterInterface
{
    /**
     * Matches the given URI against the list of configured routes.
     * @param string|uri $page
     * @return RequestHandlerInterface
     * @throws InvalidArgumentException When $uri is invalid
     * @throws NotFoundException When the requested action handler does not exist
     * @return \Exception When we don't know what happened
     */
    function match($uri): RequestHandlerInterface;

    /**
     * Returns a not found 404 handler if configured,
     * otherwise throws an exception.
     * @return RequestHandlerInterface
     * @throws NotFoundException When the requested action handler does not exist
     * @return \Exception When we don't know what happened
     */
    function getNotFoundHandler(): RequestHandlerInterface;

    /**
     * Returns an error 500 handler if configured,
     * otherwise throws an exception.
     * @return RequestHandlerInterface
     * @throws NotFoundException When the requested action handler does not exist
     * @return \Exception When we don't know what happened
     */
    function getErrorHandler(): RequestHandlerInterface;
}
