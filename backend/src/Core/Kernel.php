<?php

declare(strict_types=1);

namespace App\Core;

use App\Http\Json;
use App\Http\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Manejador más interno: enruta y despacha al controlador.
 * Ya no hace `new`: el contenedor construye el controlador y sus dependencias.
 */
final class Kernel implements RequestHandlerInterface
{
    public function __construct(
        private ContainerInterface $contenedor,
        private Router $router
    ) {
    }

    public function handle(ServerRequestInterface $peticion): ResponseInterface
    {
        $metodo = $peticion->getMethod();
        $ruta   = $peticion->getUri()->getPath();

        $encontrada = $this->router->resolver($metodo, $ruta);

        if ($encontrada !== null) {
            $controlador = $this->contenedor->get($encontrada['controlador']);

            return $controlador->{$encontrada['accion']}($peticion, $encontrada['parametros']);
        }

        $permitidos = $this->router->metodosPermitidos($ruta);

        if ($permitidos !== []) {
            return Json::respuesta(405, ['ok' => false, 'error' => 'Método no permitido'])
                ->withHeader('Allow', implode(', ', $permitidos));
        }

        return Json::respuesta(404, [
            'ok'    => false,
            'error' => 'Ruta no encontrada',
            'ruta'  => $ruta,
        ]);
    }
}
