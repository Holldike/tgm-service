<?php

interface Api
{
    public function sendMessage(string $phone);
    public function getMessage(int $message_id);
    public function login();
}