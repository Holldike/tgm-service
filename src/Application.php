<?php


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
        $routeInfo = $this->router->dispatch();
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $action = $routeInfo[1];
                $vars = $routeInfo[2];

                if ($this->requestValidator->valid($action)) {
                    $this->telegram->$action(...array_values($vars));
                } else {
                    $this->requestValidator->getErrors();
                }
                break;
        }
    }
}