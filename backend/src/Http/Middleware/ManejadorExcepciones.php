<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class ManejadorExcepciones implements MiddlewareInterface
{
    public function __construct(private bool $depurar = false)
    {
    }

    public function process(ServerRequestInterface $peticion, RequestHandlerInterface $manejador): ResponseInterface
    {
        try {
            return $manejador->handle($peticion);
        } catch (Throwable $excepcion) {
            $datos = ['ok' => false, 'error' => 'Error interno'];

            if ($this->depurar) {
                $datos['excepcion'] = $excepcion::class;
                $datos['mensaje']   = $excepcion->getMessage();
                $datos['ubicacion'] = $excepcion->getFile() . ':' . $excepcion->getLine();
            }

            return Json::respuesta(500, $datos);
        }
    }
}
