<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$request = Request::createFromGlobals();

$framework = new Framework\Simplex;

$response = $framework->handle($request);

$response->send();