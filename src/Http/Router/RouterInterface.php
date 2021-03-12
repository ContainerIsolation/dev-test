<?php
namespace Totallywicked\DevTest\Http\Router;

use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Router interface for this application.
 * Intended as an adapter for external router implementations.
 */
interface RouterInterface
{
    /**
     * Handles the given request.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws InvalidArgumentException When $request is invalid
     * @throws NotFoundException When the requested handler does not exist
     * @throws \Exception When we don't know what happened
     */
    function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Handles 404 or throws an exception if not configured.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws InvalidArgumentException When $request is invalid
     * @throws NotFoundException When the requested handler does not exist
     * @throws \Exception When we don't know what happened
     */
    function handleNotFound(ServerRequestInterface $request): ResponseInterface;

    /**
     * Handles 500 or throws an exception if not configured.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws InvalidArgumentException When $request is invalid
     * @throws NotFoundException When the requested handler does not exist
     * @throws \Exception When we don't know what happened
     */
    function handleError(ServerRequestInterface $request): ResponseInterface;
}
