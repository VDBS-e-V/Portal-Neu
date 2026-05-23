<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Throwable;

final class ErrorHandling
{
    /**
     * @param callable(Throwable): string|null $render500Html  Callback, der HTML für 500 liefert
     */
    public static function register(bool $debug, ?callable $render500Html = null): void
    {
        ini_set('display_errors', $debug ? '1' : '0');
        error_reporting(E_ALL);

        set_exception_handler(function (Throwable $e) use ($debug, $render500Html) {
            http_response_code(500);

            if ($debug) {
                header('Content-Type: text/plain; charset=utf-8');
                echo "Uncaught exception:\n\n";
                echo $e;
                return;
            }

            header('Content-Type: text/html; charset=utf-8');

            if ($render500Html) {
                try {
                    echo $render500Html($e);
                    return;
                } catch (Throwable $inner) {
                    // Fallback, falls Rendering selbst crasht
                }
            }

            echo "Ein Fehler ist aufgetreten.";
        });
    }
}