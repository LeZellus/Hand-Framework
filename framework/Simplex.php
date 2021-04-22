<?php

namespace Framework;

use Exception;
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

    public function __construct(
        UrlMatcherInterface $urlMatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver)
    {
        $this->urlMatcher = $urlMatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    public function handle(Request $request)
    {
        $this->urlMatcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response("La page n'existe pas", 404);
        } catch (Exception $e) {
            $response = new Response("Une erreur est arrivée sur le serveur", 500);
        }

        return $response;
    }
}