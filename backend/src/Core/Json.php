<?php

declare(strict_types=1);

namespace App\Core;

final class Json
{
    /** @param array<string, mixed> $datos */
    public static function responder(int $codigo, array $datos): void
    {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $datos,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
    }
}
