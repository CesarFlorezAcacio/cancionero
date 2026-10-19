<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class EcoController
{
    public function eco(ServerRequestInterface $peticion): ResponseInterface
    {
        return Json::respuesta(200, [
            'ok'          => true,
            'metodo'      => $peticion->getMethod(),
            'contentType' => $peticion->getHeaderLine('Content-Type'),
            'cuerpo'      => (string) $peticion->getBody(),
        ]);
    }
}
