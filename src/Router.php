<?php

use FastRoute\Dispatcher;
use Symfony\Component\HttpFoundation\Request;

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

    public function dispatch(Request $request)
    {
        $uri = $request->getRequestUri();

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        return $this->dispatcher->dispatch($request->getMethod(), $uri);
    }
}