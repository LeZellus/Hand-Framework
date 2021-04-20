<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$response = new Response();

$map = [
    '/hello' => 'hello.php',
    '/bye' => 'bye.php',
    '/about' => 'about.php'
];

$pathInfo = $request->getPathInfo();

if (isset($map[$pathInfo])) {
    ob_start();
    include __DIR__ . '/../src/pages/' . $map[$pathInfo];
    $response->setContent(ob_get_clean());
} else {
    $response->setContent("La page n'existe pas");
    $response->setStatusCode('404');
}

$response->send();