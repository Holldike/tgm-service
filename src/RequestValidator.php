<?php


class RequestValidator
{
    public function valid(string $request)
    {
        $error = [];

        if ($request === 'sendMessage') {
            if (!$json_request =  json_decode(file_get_contents('php://input'), true)) {
                $error[] = 'It accepts only json request';
            };

            if (!isset($json_request['text'])) {
                $error[] = 'You have to pass a message text';
            };
        }

        if ($request === 'getMessage') {
            //...
        }

        return $error;
    }

}