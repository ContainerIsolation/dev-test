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
function pageAction($dataProvider = null, $title, $templateFile = '', $layoutFile = 'layout/standard')
{
    return factory(function (ContainerInterface $c)
        use ($dataProvider, $title, $templateFile, $layoutFile)
    {
        return $c->make(\Totallywicked\DevTest\Action\PageAction::class, [
            'dataProvider' => $dataProvider ? $c->get($dataProvider) : null,
            'templateFile' => $templateFile,
            'layoutFile' => $layoutFile,
            'title' => $title,
        ]);
    });
}

/**
 * Creates and returns resource configuration
 */
function resource($className, $uri)
{
    $uriFactory = new \Laminas\Diactoros\UriFactory();
    return autowire($className)->constructorParameter('resourceUri', $uriFactory->createUri($uri));
}

return [
    \Psr\Http\Message\UriFactoryInterface::class => get(\Laminas\Diactoros\UriFactory::class),
    \GuzzleHttp\ClientInterface::class => get(\GuzzleHttp\Client::class),
    \Totallywicked\DevTest\Http\Router\RouterInterface::class =>
        autowire(\Totallywicked\DevTest\Http\Router\LeagueRouter::class)
            ->constructorParameter('notFoundHandler', get(\Totallywicked\DevTest\Action\NotFoundAction::class))
            ->constructorParameter('errorHandler', get(\Totallywicked\DevTest\Action\ErrorAction::class))
            ->constructorParameter('configuration', [
                'GET' => [
                    '/' => pageAction(null, "Home", "index"),
                    '/character/{id}' => pageAction(
                        \Totallywicked\DevTest\Model\DataProvider\CharacterProvider::class,
                        "Character",
                        "character"
                    ),
                    '/location/{id}' => pageAction(
                        \Totallywicked\DevTest\Model\DataProvider\LocationProvider::class,
                        "Location",
                        "location"
                    ),
                    '/episode/{id}' => pageAction(
                        \Totallywicked\DevTest\Model\DataProvider\EpisodeProvider::class,
                        "Episode",
                        "episode"
                    )
                ]
            ]),
    \Totallywicked\DevTest\Template\HandlebarsTemplate::class =>
        autowire(\Totallywicked\DevTest\Template\HandlebarsTemplate::class)
            ->constructorParameter('viewDirectory', dirname(__DIR__) . '/src/view'),
    \Totallywicked\DevTest\Model\Resource\CharacterResource::class =>
        resource(\Totallywicked\DevTest\Model\Resource\CharacterResource::class, 'https://rickandmortyapi.com/api/character'),
    \Totallywicked\DevTest\Model\Resource\LocationResource::class =>
        resource(\Totallywicked\DevTest\Model\Resource\LocationResource::class, 'https://rickandmortyapi.com/api/location'),
    \Totallywicked\DevTest\Model\Resource\EpisodeResource::class =>
        resource(\Totallywicked\DevTest\Model\Resource\EpisodeResource::class, 'https://rickandmortyapi.com/api/episode')
];