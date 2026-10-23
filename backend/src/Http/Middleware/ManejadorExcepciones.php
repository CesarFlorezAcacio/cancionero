<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Json;
use App\Excepcion\ErrorHttp;
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
        } catch (ErrorHttp $excepcion) {
            // Error de dominio: él decide el status (404, 409...). No es un 500.
            return Json::respuesta($excepcion->estado(), [
                'ok'    => false,
                'error' => $excepcion->getMessage(),
            ]);
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
