<?php

use FastRoute\Dispatcher;

class Router
{
    private Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
            $r->get('/api/getMessage/{message_id:\d+}', 'getMessage');
            $r->post('/api/sendMessage/{phone:\d+}', 'sendMessage');
        });
    }

    public function dispatch()
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $this->dispatcher->dispatch($httpMethod, $uri);
    }
}