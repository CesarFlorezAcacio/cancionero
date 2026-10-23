<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HolaController
{
    public function hola(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        return Json::respuesta(200, [
            'ok'      => true,
            'mensaje' => 'Hola desde PHP-FPM',
            'ruta'    => $peticion->getUri()->getPath(),
            'hora'    => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
