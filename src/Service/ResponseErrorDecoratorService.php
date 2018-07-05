<?php


namespace App\Service;


class ResponseErrorDecoratorService
{
    public function decorateError(int $status, $message)
    {
        return [
            'error' => [
                'code' => $status,
                'message' => $message
            ]
        ];
    }
}