<?php

declare(strict_types=1);

namespace App\Http;

use App\Excepcion\CuerpoMalFormado;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\{ResponseInterface,ServerRequestInterface};

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

    /** @return array<string, mixed> */
    public static function cuerpo(ServerRequestInterface $peticion): array
    {
        $datos = json_decode((string) $peticion->getBody(), true);

        if (!is_array($datos)) {
            throw new CuerpoMalFormado();
        }

        return $datos;
    }
}
