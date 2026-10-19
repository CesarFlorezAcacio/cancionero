<?php

declare(strict_types=1);

namespace App\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

final class Json
{
    /** @param array<string, mixed> $datos */
    public static function respuesta(int $codigo, array $datos): ResponseInterface
    {
        $cuerpo = json_encode(
            $datos,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );

        // Cadena de `with*`: cada uno devuelve una respuesta NUEVA.
        return (new Psr17Factory())
            ->createResponse($codigo)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody(Stream::create($cuerpo));
    }
}
