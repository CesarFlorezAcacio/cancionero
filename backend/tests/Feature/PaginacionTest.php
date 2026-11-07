<?php

declare(strict_types=1);

use App\Core\Aplicacion;
use Nyholm\Psr7\ServerRequest;

it('pagina de dos en dos', function (): void {
    $cuerpo = json_decode((string) Aplicacion::crear()
        ->procesar(new ServerRequest('GET', '/api/canciones?limite=2'))->getBody(), true);

    expect($cuerpo['canciones'])->toHaveCount(2)
        ->and($cuerpo['siguiente'])->toBeString();
});

it('el cursor trae la página siguiente sin repetir', function (): void {
    $app = Aplicacion::crear();
    $primera = json_decode((string) $app->procesar(new ServerRequest('GET', '/api/canciones?limite=2'))->getBody(), true);

    $segunda = json_decode((string) $app->procesar(new ServerRequest('GET', '/api/canciones?limite=2&despues=' . $primera['siguiente']))->getBody(), true);

    expect($segunda['canciones'])->not->toBeEmpty()
        ->and($segunda['canciones'][0]['id'])->not->toBe($primera['canciones'][0]['id']);
});

it('la última página no trae cursor', function (): void {
    $cuerpo = json_decode((string) Aplicacion::crear()
        ->procesar(new ServerRequest('GET', '/api/canciones?limite=50'))->getBody(), true);

    expect($cuerpo['siguiente'])->toBeNull();
});
