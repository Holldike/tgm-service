<?php

class Telegram implements Api
{
    private Api $driver;

    public function __construct(Api $driver)
    {
        $this->driver = $driver;
    }

    public function sendMessage(string $phone)
    {
        $this->driver->sendMessage($phone);
    }

    public function getMessage(int $messageId)
    {
        $this->driver->getMessage($messageId);
    }

    public function login()
    {
        $this->driver->login();
    }
}
