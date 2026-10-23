<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\CancionController;
use App\Controller\EcoController;
use App\Controller\FalloController;
use App\Controller\HolaController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ManejadorExcepciones;
use App\Http\{Tuberia, Router};
use App\Repository\CancionRepository;
use App\Repository\CancionRepositoryEnMemoria;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Ensamblado: contenedor, rutas, middlewares y núcleo.
 * index.php solo llama a crear() y a procesarDesdeGlobals().
 */
final class Aplicacion
{
    private function __construct(
        private Contenedor $contenedor,
        private Tuberia $tuberia,
    ) {
    }

    public static function crear(): self
    {
        ManejadorErrores::registrar();

        $contenedor = new Contenedor();
        $contenedor->set(Psr17Factory::class, static fn (): Psr17Factory => new Psr17Factory());
        $contenedor->set(
            ManejadorExcepciones::class,
            static fn (): ManejadorExcepciones => new ManejadorExcepciones(getenv('APP_DEBUG') === 'true'),
        );
        // Interfaz no instanciable -> definición explícita obligatoria.
        $contenedor->set(
            CancionRepository::class,
            static fn (): CancionRepository => new CancionRepositoryEnMemoria(),
        );

        $router = new Router();
        $router->get('/api/hola', [HolaController::class, 'hola']);
        $router->get('/api/canciones', [CancionController::class, 'listar']);
        $router->get('/api/canciones/{id}', [CancionController::class, 'ver']);
        $router->post('/api/eco', [EcoController::class, 'eco']);
        $router->get('/api/boom', [FalloController::class, 'boom']);
        $router->get('/api/aviso', [FalloController::class, 'aviso']);
        $router->get('/api/fatal', [FalloController::class, 'fatal']);
        
        $nucleo = new Kernel($contenedor, $router);

        $tuberia = new Tuberia($nucleo);
        // Orden = diseño: CORS MÁS EXTERNO, así los errores también llevan cabeceras.
        $tuberia->agregar($contenedor->get(Cors::class));
        $tuberia->agregar($contenedor->get(ManejadorExcepciones::class));

        return new self($contenedor, $tuberia);
    }

    public function procesar(ServerRequestInterface $peticion): ResponseInterface
    {
        return $this->tuberia->procesar($peticion);
    }

    public function procesarDesdeGlobals(): ResponseInterface
    {
        $psr17 = $this->contenedor->get(Psr17Factory::class);
        $peticion = (new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals();

        return $this->procesar($peticion);
    }
}
