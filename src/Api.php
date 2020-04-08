<?php

interface Api
{
    public function sendMessage(string $phone);
    public function getMessage(int $messageId);
    public function login();
}