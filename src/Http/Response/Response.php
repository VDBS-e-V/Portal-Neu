<?php

declare(strict_types=1);

namespace App\Http\Response;

class Response
{
    public function __construct(
        protected int $status = 200,
        protected array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
        protected string $body = ''
    ) {}

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $k => $v) {
            header($k . ': ' . $v);
        }
        echo $this->body;
    }
}