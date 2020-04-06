<?php


class RequestValidator
{
    private array $errors;

    public function valid(string $request)
    {
        return !$this->errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}