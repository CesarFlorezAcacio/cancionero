<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Kernel;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;

$fallos = 0;

/** @param mixed $obtenido @param mixed $esperado */
function comprobar(string $nombre, $obtenido, $esperado): void
{
    global $fallos;
    $ok = $obtenido === $esperado;
    printf("%s  %s\n", $ok ? 'OK   ' : 'FALLA', $nombre);
    if (!$ok) {
        printf("       esperado: %s | obtenido: %s\n", var_export($esperado, true), var_export($obtenido, true));
        $fallos++;
    }
}

$kernel = new Kernel();

$casos = [
    ['GET',  '/api/hola',            200],
    ['GET',  '/api/canciones/7',     200],
    ['GET',  '/api/canciones/7/x',   404],   // [^/]+ no cruza barras
    ['POST', '/api/hola',            405],   // ruta existe, método no
    ['GET',  '/api/nope',            404],
    ['GET',  '/api/hola/',           404],   // barra final no coincide
];

foreach ($casos as [$metodo, $ruta, $esperado]) {
    $peticion  = new ServerRequest($metodo, $ruta);
    $respuesta = $kernel->handle($peticion);
    comprobar("$metodo $ruta -> $esperado", $respuesta->getStatusCode(), $esperado);
}

// El 405 debe traer Allow
$respuesta = $kernel->handle(new ServerRequest('POST', '/api/hola'));
comprobar('405 trae Allow: GET', $respuesta->getHeaderLine('Allow'), 'GET');

// El parámetro se captura
$respuesta = $kernel->handle(new ServerRequest('GET', '/api/canciones/42'));
$cuerpo    = json_decode((string) $respuesta->getBody(), true);
comprobar('parametro id capturado', $cuerpo['id'], '42');

// INMUTABILIDAD: with* devuelve copia, no muta
$original = (new Psr17Factory())->createResponse(200);
$original->withStatus(404);
comprobar('withStatus sin reasignar NO cambia', $original->getStatusCode(), 200);

$otra = $original->withStatus(404);
comprobar('withStatus reasignado SI cambia', $otra->getStatusCode(), 404);
comprobar('el original sigue intacto', $original->getStatusCode(), 200);

exit($fallos === 0 ? 0 : 1);
