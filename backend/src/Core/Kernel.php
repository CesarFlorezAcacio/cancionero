<?php

declare(strict_types=1);

namespace App\Core;

use App\Controller\HolaController;

/**
 * Núcleo mínimo. Hoy solo demuestra dos cosas:
 *   1. que Composer carga clases de namespaces distintos sin `require`
 *   2. que el front controller es el único punto de entrada
 *
 * Día 5: este `match` se sustituye por un Router de verdad.
 */
final class Kernel
{
    public function run(): void
    {
        $ruta = $this->rutaActual();

        [$codigo, $cuerpo] = match ($ruta) {
            '/api/hola' => [200, (new HolaController())->hola()],
            default     => [404, $this->noEncontrada($ruta)],
        };

        Json::responder($codigo, $cuerpo);
    }

    private function rutaActual(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    /** @return array<string, mixed> */
    private function noEncontrada(string $ruta): array
    {
        return [
            'ok'    => false,
            'error' => 'Ruta no encontrada',
            'ruta'  => $ruta,
        ];
    }
}
