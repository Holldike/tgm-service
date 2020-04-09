<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Logger
{
    public function requestLog(Request $request, Response $response)
    {
        Db::getConnect()->query(
            "INSERT INTO log SET 
                    request_body = '" . $request->getContent() . "',
                    respons_body = '" . $response->getContent() . "',
                    sent_at = '" . date("Y-m-d H:i:s") . "'
                    ");
    }
}