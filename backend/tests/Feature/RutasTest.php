<?php

declare(strict_types=1);

use App\Core\Aplicacion;
use Nyholm\Psr7\ServerRequest;

it('responde el status esperado', function (string $metodo, string $ruta, int $estado): void {
    $respuesta = Aplicacion::crear()->procesar(new ServerRequest($metodo, $ruta));

    expect($respuesta->getStatusCode())->toBe($estado);
})->with([
    'hola'            => ['GET', '/api/hola', 200],
    'cancion'         => ['GET', '/api/canciones/1', 200],
    'inexistente'     => ['GET', '/api/canciones/999', 404],
    'ruta profunda'   => ['GET', '/api/canciones/7/x', 404],
    'verbo incorrecto'=> ['POST', '/api/hola', 405],
    'ruta ausente'    => ['GET', '/api/nope', 404],
]);

it('el 405 trae Allow', function (): void {
    $respuesta = Aplicacion::crear()->procesar(new ServerRequest('POST', '/api/hola'));

    expect($respuesta->getHeaderLine('Allow'))->toContain('GET');
});

it('captura el parámetro de ruta', function (): void {
    $respuesta = Aplicacion::crear()->procesar(new ServerRequest('GET', '/api/canciones/2'));

    expect(json_decode((string) $respuesta->getBody(), true)['cancion']['id'])->toBe('2');
});

it('la excepción sale como 500 JSON', function (): void {
    $respuesta = Aplicacion::crear()->procesar(new ServerRequest('GET', '/api/boom'));

    expect($respuesta->getStatusCode())->toBe(500)
        ->and($respuesta->getHeaderLine('Content-Type'))->toContain('application/json');
});
