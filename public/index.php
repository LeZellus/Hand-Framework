<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$response = new Response();

$routes = require __DIR__ . '/../src/routes.php';

$context = new RequestContext();
$context->fromRequest($request);

$urlMatcher = new UrlMatcher($routes, $context);

try {
    $result = $urlMatcher->match($request->getPathInfo());
    $request->attributes->add($result);

    // Class to be instancied
    $className = substr(
        $result['_controller'],
        0,
        strpos($result['_controller'], '@')
    );

    // Method to be call
    $methodName = substr(
        $result['_controller'],
        strpos($result['_controller'], '@') + 1
    );

    // Callable
    $controller = [new $className, $methodName];

    $response = call_user_func($controller, $request);

} catch (ResourceNotFoundException $e) {
    $response = new Response("La page n'existe pas", 404);
} catch (Exception $e) {
    $response = new Response("Une erreur est arrivée sur le serveur", 500);
}

$response->send();