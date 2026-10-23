<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Aplicacion;
use App\Core\Contenedor;
use Nyholm\Psr7\ServerRequest;

$fallos = 0;

function comprobar(string $nombre, mixed $obtenido, mixed $esperado): void
{
    global $fallos;
    $ok = $obtenido === $esperado;
    printf("%s\t%s\n", $ok ? 'OK' : 'FALLA', $nombre);
    if (!$ok) {
        printf("\tesperado: %s | obtenido: %s\n", var_export($esperado, true), var_export($obtenido, true));
        $fallos++;
        echo "$fallos\n";
    }
}

final class Dependencia
{
}

final class Servicio
{
    public function __construct(public Dependencia $dependencia)
    {
    }
}

$app = Aplicacion::crear();

$casos = [
    ['GET',  '/api/hola',          200],
    ['GET',  '/api/canciones/1',   200],
    ['GET',  '/api/canciones/999', 404],   // antes era /7 -> 200
    ['GET',  '/api/canciones/7/x', 404],
    ['POST', '/api/hola',          405],
    ['GET',  '/api/nope',          404],
    ['GET',  '/api/hola/',         404],
];

foreach ($casos as [$metodo, $ruta, $esperado]) {
    $respuesta = $app->procesar(new ServerRequest($metodo, $ruta));
    comprobar("$metodo $ruta -> $esperado", $respuesta->getStatusCode(), $esperado);
}

$respuesta = $app->procesar(new ServerRequest('POST', '/api/hola'));
comprobar('405 trae Allow: GET', $respuesta->getHeaderLine('Allow'), 'GET');

$respuesta = $app->procesar(new ServerRequest('GET', '/api/canciones/2'));
comprobar('parametro id capturado', json_decode((string) $respuesta->getBody(), true)['cancion']['id'], '2');

// Middleware CORS
$respuesta = $app->procesar(new ServerRequest('GET', '/api/hola'));
comprobar('CORS presente', $respuesta->getHeaderLine('Access-Control-Allow-Origin'), '*');
$respuesta = $app->procesar(new ServerRequest('OPTIONS', '/api/hola'));
comprobar('preflight OPTIONS -> 204', $respuesta->getStatusCode(), 204);

// Middleware de excepciones
$respuesta = $app->procesar(new ServerRequest('GET', '/api/boom'));
comprobar('excepcion -> 500', $respuesta->getStatusCode(), 500);
comprobar('excepcion -> JSON', $respuesta->getHeaderLine('Content-Type'), 'application/json; charset=utf-8');

// Error handler: un aviso de PHP se convierte en 500
$respuesta = $app->procesar(new ServerRequest('GET', '/api/aviso'));
comprobar('aviso PHP -> 500', $respuesta->getStatusCode(), 500);

// Contenedor: singleton y autowiring
$contenedor = new Contenedor();
$contenedor->set('servicio', static fn (): object => new stdClass());
comprobar('contenedor singleton', $contenedor->get('servicio') === $contenedor->get('servicio'), true);
comprobar('autowiring construye', $contenedor->get(Servicio::class) instanceof Servicio, true);

// Inmutabilidad (regresión Día 5)
$original = (new Nyholm\Psr7\Factory\Psr17Factory())->createResponse(200);
$original->withStatus(404);
comprobar('withStatus sin reasignar NO cambia', $original->getStatusCode(), 200);

exit($fallos === 0 ? 0 : 1);
