<?php
use Psr\Container\ContainerInterface;
use DI\Factory\RequestedEntry;
use function DI\autowire;
use function DI\factory;
use function DI\create;
use function DI\get;

/**
 * Creates and returns page action configuration
 */
function pageAction($title, $templateFile, $layoutFile = 'layout/standard')
{
    return autowire(\Totallywicked\DevTest\Action\PageAction::class)
        ->constructorParameter('templateFile', $templateFile)
        ->constructorParameter('layoutFile', $layoutFile)
        ->constructorParameter('title', $title);
}

return [
    \Psr\Http\Message\UriFactoryInterface::class => get(\Laminas\Diactoros\UriFactory::class),
    \GuzzleHttp\ClientInterface::class => get(\GuzzleHttp\Client::class),
    'api' => [
        'baseUri' => 'https://rickandmortyapi.com',
        'charactersPath' => '/api/character',
        'episodesPath' => '/api/episode',
        'locationPath' => '/api/location'
    ],
    \Totallywicked\DevTest\Http\Router\RouterInterface::class => autowire(\Totallywicked\DevTest\Http\Router\LeagueRouter::class)
        ->constructorParameter('notFoundHandler', get(\Totallywicked\DevTest\Action\NotFoundAction::class))
        ->constructorParameter('errorHandler', get(\Totallywicked\DevTest\Action\ErrorAction::class))
        ->constructorParameter('configuration', [
            'GET' => [
                '/' => pageAction("Home", "index")
            ]
        ]),
    \Totallywicked\DevTest\Template\HandlebarsTemplate::class => autowire(\Totallywicked\DevTest\Template\HandlebarsTemplate::class)
        ->constructorParameter('viewDirectory', dirname(__DIR__) . '/src/view')
];