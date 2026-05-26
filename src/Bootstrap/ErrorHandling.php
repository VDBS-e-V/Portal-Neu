<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Http\Response\JsonResponse;
use Throwable;

final class ErrorHandling
{
    public static function register(bool $debug, ?callable $renderHtml500 = null): void
    {
        ini_set('display_errors', $debug ? '1' : '0');
        error_reporting(E_ALL);

        set_exception_handler(static function (Throwable $throwable) use ($debug, $renderHtml500): void {
            http_response_code(500);

            if ($debug) {
                header('Content-Type: text/plain; charset=utf-8');
                echo (string) $throwable;

                return;
            }

            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $path = is_string($path) && $path !== '' ? $path : '/';

            if (str_starts_with($path, '/api')) {
                (new JsonResponse(['status' => 'error', 'message' => 'Internal Server Error'], 500))->send();

                return;
            }

            if ($renderHtml500 !== null) {
                try {
                    header('Content-Type: text/html; charset=utf-8');
                    echo $renderHtml500();

                    return;
                } catch (Throwable) {
                }
            }

            header('Content-Type: text/plain; charset=utf-8');
            echo 'Internal Server Error';
        });
    }
}