<?php

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Contracts\EventDispatcher\Event;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$routes = require __DIR__ . '/../src/routes.php';

$context = new RequestContext();

$urlMatcher = new UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();
$dispatcher = new EventDispatcher();

$dispatcher->addListener('kernel.request', function(){
    dump('salaud');
});

$dispatcher->addListener('kernel.controller', function(){
    dump('hello');
});

$framework = new Framework\Simplex($dispatcher, $urlMatcher, $controllerResolver, $argumentResolver);

$response = $framework->handle($request);

$response->send();