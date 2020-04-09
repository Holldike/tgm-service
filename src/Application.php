<?php

use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                break;

            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                //$allowedMethods = $routeInfo[1];
                break;

            case FastRoute\Dispatcher::FOUND:
                $action = $routeInfo[1];
                $params = $routeInfo[2];
                $params['request'] = $this->request;

                if (!$errors = $this->validator->token((string)$this->request->get('token'))) {

                    if (!$errors = $this->validator->methodData($action)) {
                        $responseData = $this->telegram->$action(...array_values($params));
                    } else {
                        $responseData = [
                            'status' => 'error',
                            'description' => $errors
                        ];
                    }

                } else {
                    $responseData = [
                        'status' => 'error',
                        'description' => $errors
                    ];
                }

                $response->setContent(json_encode($responseData));
                break;
        }

        $response->headers->set('Content-Type', 'application/json');
        $this->logger->requestLog($this->request, $response);
        return $response;
    }
}