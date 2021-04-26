<?php

namespace Framework;

use Exception;
use Framework\Event\ArgumentEvent;
use Framework\Event\ControllerEvent;
use Framework\Event\RequestEvent;
use Framework\Event\ResponseEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Simplex
{
    protected UrlMatcherInterface $urlMatcher;
    protected ControllerResolverInterface $controllerResolver;
    protected ArgumentResolverInterface $argumentResolver;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UrlMatcherInterface $urlMatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver)
    {
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(Request $request)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $this->eventDispatcher->dispatch(new RequestEvent($request), 'kernel.request');

            $controller = $this->controllerResolver->getController($request);
            $this->eventDispatcher->dispatch(new ControllerEvent($request, $controller), 'kernel.controller');

            $arguments = $this->argumentResolver->getArguments($request, $controller);
            $this->eventDispatcher->dispatch(new ArgumentEvent($request, $controller, $arguments), 'kernel.arguments');

            $response = call_user_func_array($controller, $arguments);
            $this->eventDispatcher->dispatch(new ResponseEvent($response), 'kernel.response');
        } catch (ResourceNotFoundException $e) {
            $response = new Response("La page n'existe pas", 404);
        } catch (Exception $e) {
            $response = new Response("Une erreur est arrivée sur le serveur", 500);
        }

        return $response;
    }
}