<?php


class RequestValidator
{
    private array $errors;

    public function valid(string $request)
    {
        if ($request === 'sendMessage') {
            if (!$json_request = (json_decode($_POST))) {
                $this->errors[] = 'It accepts only json request';
            };

            if (!isset($json_request['text'])) {
                $this->errors[] = 'You have to pass a message text';
            };
        }

        if ($request === 'getMessage') {
            //...
        }

        return $this->errors;
    }

}