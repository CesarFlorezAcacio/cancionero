<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CancionController
{
    /** Día 7 esto consultará Firestore. Hoy solo prueba la captura. */
    public function ver(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        return Json::respuesta(200, [
            'ok'       => true,
            'id'       => $args['id'],
            'origen'   => 'parametro de ruta capturado por el Router',
            'metodo'   => $peticion->getMethod(),
        ]);
    }
}
