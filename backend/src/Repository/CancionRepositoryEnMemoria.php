<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Cancion;

/** Implementación de trabajo para hoy: sin red, sin credenciales, testeable. */
final class CancionRepositoryEnMemoria implements CancionRepository
{
    /** @var array<string, Cancion> */
    private array $canciones;
    private int $siguiente = 4;

    public function __construct()
    {
        $this->canciones = [
            '1' => new Cancion('1', 'Cielito lindo', 'Quirino Mendoza', 'Do mayor', [
                'De la sierra morena, cielito lindo, vienen bajando',
                'un par de ojitos negros, cielito lindo, de contrabando.',
            ]),
            '2' => new Cancion('2', 'La bamba', 'Tradicional', 'Do mayor', [
                'Para bailar la bamba se necesita una poca de gracia',
                'una poca de gracia y otra cosita, y arriba y arriba.',
            ]),
            '3' => new Cancion('3', 'Las mañanitas', 'Tradicional', 'Sol mayor', [
                'Estas son las mañanitas que cantaba el rey David',
                'hoy por ser día de tu santo te las cantamos a ti.',
            ]),
        ];
    }

    public function buscarPorId(string $id): ?Cancion
    {
        return $this->canciones[$id] ?? null;
    }

    /** @return list<Cancion> */
    public function todas(): array
    {
        return array_values($this->canciones);
    }

    public function guardar(Cancion $cancion): void
    {
        $this->canciones[$cancion->id] = $cancion;
    }

    public function eliminar(string $id): void
    {
        unset($this->canciones[$id]);
    }

    public function nuevoId(): string
    {
        return (string) $this->siguiente++;
    }
}
