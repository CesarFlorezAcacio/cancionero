<?php

declare(strict_types=1);

use App\Core\Aplicacion;
use Nyholm\Psr7\ServerRequest;

function json(string $metodo, string $ruta, array $cuerpo = []): \Psr\Http\Message\ResponseInterface
{
    $peticion = (new ServerRequest($metodo, $ruta))
        ->withHeader('Content-Type', 'application/json');

    $peticion->getBody()->write(json_encode($cuerpo));

    return Aplicacion::crear()->procesar($peticion);
}

it('crea una canción con 201 y Location', function (): void {
    $respuesta = json('POST', '/api/canciones', [
        'titulo' => 'La llorona', 'artista' => 'Tradicional', 'tono' => 'Re menor', 'versos' => ['Salías del templo un día'],
    ]);

    expect($respuesta->getStatusCode())->toBe(201)
        ->and($respuesta->getHeaderLine('Location'))->toStartWith('/api/canciones/');
});

it('actualiza una canción existente', function (): void {
    $respuesta = json('PUT', '/api/canciones/1', ['titulo' => 'Otro título']);

    expect($respuesta->getStatusCode())->toBe(200)
        ->and(json_decode((string) $respuesta->getBody(), true)['cancion']['titulo'])->toBe('Otro título');
});

it('404 al actualizar una que no existe', function (): void {
    expect(json('PUT', '/api/canciones/999', ['titulo' => 'x'])->getStatusCode())->toBe(404);
});

it('borra y luego da 404', function (): void {
    $app = Aplicacion::crear();

    $peticionDelete = (new ServerRequest('DELETE', '/api/canciones/2'))
        ->withHeader('Content-Type', 'application/json');

    expect($app->procesar($peticionDelete)->getStatusCode())->toBe(200);

    $peticionGet = new ServerRequest('GET', '/api/canciones/2');

    expect($app->procesar($peticionGet)->getStatusCode())->toBe(404);
});

it('415 si falta el Content-Type', function (): void {
    $peticion = new ServerRequest('POST', '/api/canciones');
    $peticion->getBody()->write('{"titulo":"x"}');

    expect(Aplicacion::crear()->procesar($peticion)->getStatusCode())->toBe(415);
});

it('400 si el cuerpo no es JSON', function (): void {
    $peticion = (new ServerRequest('POST', '/api/canciones'))->withHeader('Content-Type', 'application/json');
    $peticion->getBody()->write('{roto');

    expect(Aplicacion::crear()->procesar($peticion)->getStatusCode())->toBe(400);
});
