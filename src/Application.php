<?php

use \Symfony\Component\HttpFoundation\Response;

class Application
{
    private Telegram $telegram;
    private Router $router;
    private RequestValidator $requestValidator;

    public function __construct(Router $router, Telegram $telegram, RequestValidator $requestValidator)
    {
        $this->router = $router;
        $this->requestValidator = $requestValidator;
        $this->telegram = $telegram;
    }

    public function run()
    {
        $response = new Response();
        $routeInfo = $this->router->dispatch();

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                //$allowedMethods = $routeInfo[1];
                break;

            case FastRoute\Dispatcher::FOUND:
                $action = $routeInfo[1];
                $vars = $routeInfo[2];

                if (!$errors = $this->requestValidator->valid($action)) {
                    $responseData = $this->telegram->$action(...array_values($vars));
                } else {
                    $responseData = $errors;
                }

                $response->setContent(json_encode([$responseData]));
                break;
        }

        return $response;
    }
}