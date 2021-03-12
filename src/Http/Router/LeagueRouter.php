<?php
namespace Totallywicked\DevTest\Http\Router;

use Psr\Container\ContainerInterface;
use Totallywicked\DevTest\Exception\InvalidArgumentException;
use Totallywicked\DevTest\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Router;

/**
 * Router interface for this application.
 * Intended as an adapter for external router implementations.
 * This router does not support middlewares are they are not a requirement of this project.
 */
class LeagueRouter implements RouterInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestHandlerInterface
     */
    protected $notFoundHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $errorHandler;

    /**
     * Container configuration of all our routes.
     * @var array
     */
    protected $configuration;

    /**
     * Constructor
     * @param array $configuration
     */
    public function __construct(
        ContainerInterface $container,
        RequestHandlerInterface $notFoundHandler = null,
        RequestHandlerInterface $errorHandler = null,
        array $configuration
    ) {
        $this->container = $container;
        $this->notFoundHandler = $notFoundHandler;
        $this->errorHandler = $errorHandler;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    function match(ServerRequestInterface $request): RequestHandlerInterface
    {
        $router = $this->getRouter();
        try {
            return $router->dispatch($request);
        } catch (\League\Route\Http\Exception\NotFoundException $th) {
            if ($this->notFoundHandler) {
                return $this->notFoundHandler;
            }
        }
        if ($this->errorHandler) {
            $this->errorHandler->setError();
            return $this->errorHandler;
        }
    }

    /**
     * @inheritDoc
     */
    function getNotFoundHandler(): RequestHandlerInterface
    {
        return $this->notFoundHandler;
    }

    /**
     * @inheritDoc
     */
    function getErrorHandler(): RequestHandlerInterface
    {
        return $this->errorHandler;
    }

    /**
     * Configures and returns a new router instance
     * This is required as League router does not work with
     * multiple consecutive requests.
     * @return Router
     */
    protected function getRouter()
    {
        $applicationStrategy = $this->container->make(ApplicationStrategy::class);
        $applicationStrategy->setContainer($this->container);
        $router = $this->container->make(Router::class);
        $router->setStrategy($applicationStrategy);

        foreach($this->configuration as $method => $routes) {
            foreach($routes as $path => $className) {
                if (is_a($className, RequestHandlerInterface::class, true)) {
                    $router->map($method, $path, [$className, 'handle']);
                }
            }
        }
        return $router;
    }
}
