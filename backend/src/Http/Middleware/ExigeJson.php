<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** Preocupación transversal de HTTP: los verbos con cuerpo exigen JSON. */
final class ExigeJson implements MiddlewareInterface
{
    public function process(ServerRequestInterface $peticion, RequestHandlerInterface $manejador): ResponseInterface
    {
        $esRutaDeCanciones = str_starts_with($peticion->getUri()->getPath(), '/api/canciones');
        $verboConCuerpo = in_array($peticion->getMethod(), ['POST', 'PUT', 'PATCH'], true);
        $tieneCabeceraJson = str_contains($peticion->getHeaderLine('Content-Type'), 'application/json');

        if ($esRutaDeCanciones && $verboConCuerpo && !$tieneCabeceraJson) {
            return Json::respuesta(415, [
                'ok'    => false,
                'error' => 'Se espera Content-Type: application/json.',
            ]);
        }

        return $manejador->handle($peticion);
    }
}
