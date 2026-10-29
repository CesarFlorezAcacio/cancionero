<?php

declare(strict_types=1);

use App\Excepcion\CancionNoEncontrada;
use App\Model\Cancion;
use App\Service\CancionService;
use App\Tests\Support\RepoFalso;

it('devuelve la canción pedida', function (): void {
    expect((new CancionService(new RepoFalso()))->obtener('1')->titulo)->toBe('Titulo falso');
});

it('devuelve un objeto del dominio, no un array', function (): void {
    expect((new CancionService(new RepoFalso()))->obtener('1'))->toBeInstanceOf(Cancion::class);
});

it('lanza CancionNoEncontrada con status 404', function (): void {
    $servicio = new CancionService(new RepoFalso());

    expect(fn () => $servicio->obtener('999'))->toThrow(CancionNoEncontrada::class);

    try {
        $servicio->obtener('999');
    } catch (CancionNoEncontrada $e) {
        expect($e->estado())->toBe(404);
    }
});

it('tolera campos ausentes al hidratar', function (): void {
    expect(Cancion::desdeArray(['id' => 'x'])->artista)->toBe('Desconocido');
});
