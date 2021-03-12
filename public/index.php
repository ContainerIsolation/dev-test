<?php
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
$diConfig = dirname(__DIR__) . '/env/config.php';

// Handle error
function handleError($message, $code = 500)
{
    $message = htmlentities($message);
    http_response_code($code);
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$code | Rick and Morty</title>
    <link rel="stylesheet" href="/static/styles/style.css">
</head>
<body>
    <h1>Server error $code</h1>
    <p>$message</p>
</body>
</html>
HTML;
    exit(1);
}

// Basic error handler wrapper
// set_error_handler(function($errno, $errstr, $errfile, $errline)
// {
//     handleError("There was an unknown error!");
// });

// Check if the file exists
if (!file_exists($autoload)) {
    handleError("autoload.php could not be found, did you run composer install?");
}

// Attempt to load the file
try {
    require_once $autoload;
} catch (\Exception $e) {
    handleError($e->getMessage());
}

// Configure and create the container
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions($diConfig);
$container = $containerBuilder->build();

// Get the router, request and find the matching action
$router = $container->get(\Totallywicked\DevTest\Http\Router\RouterInterface::class);
$emitter = $container->get(\Laminas\HttpHandlerRunner\Emitter\SapiEmitter::class);
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);
$action = $router->match($request);
try {
    $response = $action->handle($request);
    $emitter->emit($response);
} catch (\Throwable $th) {
    $errorHandler = $router->getErrorHandler();
    $errorHandler->setError($th);
    $response = $errorHandler->handle($request);
    $emitter->emit($response);
}
