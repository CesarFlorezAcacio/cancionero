<?php

declare(strict_types=1);

use App\Excepcion\ErrorValidacion;
use App\Service\Validacion\ValidadorCancion;

it('rechaza un título vacío', function (): void {
    expect(fn () => (new ValidadorCancion())->validar(['versos' => ['a']]))
        ->toThrow(ErrorValidacion::class);
});

it('acumula el detalle por campo', function (): void {
    try {
        (new ValidadorCancion())->validar([]);
    } catch (ErrorValidacion $e) {
        expect($e->estado())->toBe(422)
            ->and($e->datos()['errores'])->toHaveKeys(['titulo', 'versos']);
    }
});

it('normaliza espacios', function (): void {
    $limpio = (new ValidadorCancion())->validar(['titulo' => '  La llorona  ', 'versos' => ['  verso  ']]);

    expect($limpio['titulo'])->toBe('La llorona')
        ->and($limpio['versos'])->toBe(['verso']);
});
