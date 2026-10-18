<?php

declare(strict_types=1);

namespace App\Controller;

final class HolaController
{
    /** @return array<string, mixed> */
    public function hola(): array
    {
        return [
            'ok'      => true,
            'mensaje' => 'Hola desde PHP-FPM',
            'ruta'    => '/api/hola',
            'hora'    => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];
    }
}
