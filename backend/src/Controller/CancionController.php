<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Json;
use App\Service\CancionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CancionController
{
    public function __construct(private CancionService $servicio)
    {
    }

    /** @param array<string, string> $args */
    public function ver(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        $cancion = $this->servicio->obtener($args['id']);

        return Json::respuesta(200, ['ok' => true, 'cancion' => $cancion->aArray()]);
    }

    /** @param array<string, string> $args */
    public function listar(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        $canciones = array_map(
            static fn ($cancion): array => $cancion->aArray(),
            $this->servicio->listar(),
        );

        return Json::respuesta(200, ['ok' => true, 'canciones' => $canciones]);
    }
}
