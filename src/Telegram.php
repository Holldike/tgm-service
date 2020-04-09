<?php

use Symfony\Component\HttpFoundation\Request;

class Telegram implements Api
{
    private Api $driver;

    public function __construct(Api $driver)
    {
        $this->driver = $driver;
    }

    public function sendMessage(string $phone, Request $request)
    {
        return $this->driver->sendMessage($phone, $request);
    }

    public function getMessage(int $messageId, Request $request)
    {
        return $this->driver->getMessage($messageId, $request);
    }

    public function login()
    {
        $this->driver->login();
    }
}
