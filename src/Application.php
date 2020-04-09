<?php

use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FastRoute\Dispatcher;

class Application
{
    private Validator $validator;
    private Telegram $telegram;
    private Request $request;
    private Router $router;
    private Logger $logger;

    public function __construct(
        Validator $requestValidator,
        Telegram $telegram,
        Request $request,
        Logger $logger,
        Router $router
    )
    {
        $this->validator = $requestValidator;
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
            case Dispatcher::NOT_FOUND:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $content = ['status' => 'error', 'description' => 'NOT FOUND'];
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                $content = ['status' => 'error', 'description' => 'METHOD NOT ALLOWED'];
                break;

            case Dispatcher::FOUND:
                $action = $routeInfo[1];

                $params = $routeInfo[2];
                $params['request'] = $this->request;

                if ($errors = $this->validator->token((string)$this->request->get('token'))) {
                    $content = ['status' => 'error', 'description' => $errors];
                    break;
                }

                if ($errors = $this->validator->methodData($action)) {
                    $content = ['status' => 'error', 'description' => $errors];
                    break;
                }

                $content = $this->telegram->$action(...array_values($params));
                break;

            default:
                $content = null;
                break;
        }

        $response->headers->set('Content-Type', 'application/json');
        $this->logger->requestLog($this->request, $response);
        $response->setContent(json_encode($content));
        return $response;
    }
}