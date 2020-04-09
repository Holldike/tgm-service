<?php

use Symfony\Component\HttpFoundation\Request;

interface Api
{
    public function sendMessage(string $phone, Request $request);
    public function getMessage(int $messageId, Request $request);
    public function login();
}