<?php

class Validator
{
    public function methodData(string $request)
    {
        $errors = [];
        if ($request === 'sendMessage') {
            if (!$json_request = json_decode(file_get_contents('php://input'), true)) {
                $errors[] = 'it accepts only json request';
            };

            if (!isset($json_request['text'])) {
                $errors[] = 'you have to pass a message text';
            };
        }

        if ($request === 'getMessage') {
            //...
        }

        return $errors;
    }

    public function token(string $token)
    {
        $errors = [];
        if ($token !== TOKEN) {
            $errors[] = 'permission denied';
        }

        return $errors;
    }

}