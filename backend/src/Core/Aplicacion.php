<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\CancionController;
use App\Controller\EcoController;
use App\Controller\FalloController;
use App\Controller\HolaController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ExigeJson;
use App\Http\Middleware\ManejadorErrores;
use App\Http\Middleware\ManejadorExcepciones;
use App\Http\{Tuberia, Router};
use App\Repository\CancionRepository;
use App\Repository\CancionRepositoryEnMemoria;
use App\Repository\CancionRepositoryFirestore;
use App\Repository\ConexionFirestore;
use Google\Cloud\Firestore\FirestoreClient;
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

        $contenedor = new Contenedor();
        $contenedor->set(Psr17Factory::class, static fn (): Psr17Factory => new Psr17Factory());
        $contenedor->set(
            ManejadorExcepciones::class,
            static fn (): ManejadorExcepciones => new ManejadorExcepciones(getenv('APP_DEBUG') === 'true'),
        );
        $contenedor->set(FirestoreClient::class, static fn (): FirestoreClient => ConexionFirestore::crear());
        // La única línea que cambia de fuente. Ni el servicio ni el controlador se enteran.
        $contenedor->set(
            CancionRepository::class,
            static fn (Contenedor $c): CancionRepository => getenv('REPO') === 'firestore'
                ? new CancionRepositoryFirestore($c->get(FirestoreClient::class))
                : new CancionRepositoryEnMemoria(),
        );

        $router = new Router();
        $router->get('/api/hola', [HolaController::class, 'hola']);
        $router->get('/api/canciones', [CancionController::class, 'listar']);
        $router->get('/api/canciones/{id}', [CancionController::class, 'ver']);
        $router->post('/api/canciones', [CancionController::class, 'crear']);
        $router->put('/api/canciones/{id}', [CancionController::class, 'actualizar']);
        $router->delete('/api/canciones/{id}', [CancionController::class, 'eliminar']);
        $router->post('/api/eco', [EcoController::class, 'eco']);
        $router->get('/api/boom', [FalloController::class, 'boom']);
        $router->get('/api/aviso', [FalloController::class, 'aviso']);
        $router->get('/api/fatal', [FalloController::class, 'fatal']);
        
        $nucleo = new Kernel($contenedor, $router);

        $tuberia = new Tuberia($nucleo);
        // Orden = diseño: CORS MÁS EXTERNO, así los errores también llevan cabeceras.
        $tuberia->agregar($contenedor->get(Cors::class));
        $tuberia->agregar($contenedor->get(ManejadorExcepciones::class));
        $tuberia->agregar(new ExigeJson());

        return new self($contenedor, $tuberia);
    }

    public function procesar(ServerRequestInterface $peticion): ResponseInterface
    {
        ManejadorErrores::registrar();

        try {
            return $this->tuberia->procesar($peticion);
        } finally {
            ManejadorErrores::restaurar();
        }
    }

    public function procesarDesdeGlobals(): ResponseInterface
    {
        $psr17 = $this->contenedor->get(Psr17Factory::class);
        $peticion = (new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals();

        return $this->procesar($peticion);
    }
}
