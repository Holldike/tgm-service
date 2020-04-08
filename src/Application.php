<?php

use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Application
{
    private RequestValidator $requestValidator;
    private Telegram $telegram;
    private Request $request;
    private Router $router;
    private Logger $logger;

    public function __construct(
        RequestValidator $requestValidator,
        Telegram $telegram,
        Request $request,
        Logger $logger,
        Router $router
    )
    {
        $this->requestValidator = $requestValidator;
        $this->telegram = $telegram;
        $this->request = $request;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function run()
    {
        $response = new Response();
        $routeInfo = $this->router->dispatch($this->request);

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

        $this->logger->log($this->request, $response);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}