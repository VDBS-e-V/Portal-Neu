<?php

declare(strict_types=1);

namespace App\Http\Response;

class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        protected int $status = 200,
        protected array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
        protected string $body = ''
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}