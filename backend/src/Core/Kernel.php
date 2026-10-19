<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\CancionController;
use App\Controller\EcoController;
use App\Controller\HolaController;
use App\Http\Json;
use App\Http\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Núcleo. Ya no lee superglobales ni imprime:
 * recibe un objeto PSR-7 y devuelve un objeto PSR-7.
 */
final class Kernel
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registrarRutas();
    }

    public function handle(ServerRequestInterface $peticion): ResponseInterface
    {
        $metodo = $peticion->getMethod();
        $ruta   = $peticion->getUri()->getPath();

        $encontrada = $this->router->resolver($metodo, $ruta);

        if ($encontrada !== null) {
            $manejador = $encontrada['manejador'];

            return $manejador($peticion, $encontrada['parametros']);
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

    private function registrarRutas(): void
    {
        $this->router->get(
            '/api/hola',
            static fn (ServerRequestInterface $p, array $args): ResponseInterface
                => (new HolaController())->hola($p),
        );

        $this->router->get(
            '/api/canciones/{id}',
            static fn (ServerRequestInterface $p, array $args): ResponseInterface
                => (new CancionController())->ver($p, $args['id']),
        );

        $this->router->post(
            '/api/eco',
            static fn (ServerRequestInterface $p, array $args): ResponseInterface
                => (new EcoController())->eco($p),
        );
    }
}
