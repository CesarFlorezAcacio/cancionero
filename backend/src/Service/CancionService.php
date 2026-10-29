<?php

declare(strict_types=1);

namespace App\Service;

use App\Excepcion\CancionNoEncontrada;
use App\Model\Cancion;
use App\Repository\CancionRepository;

/** Reglas de negocio de canciones. No sabe de HTTP ni de Firestore. */
final class CancionService
{
    public function __construct(private CancionRepository $repositorio)
    {
    }

    /** La regla "un id que no existe es un 404" vive aquí, una sola vez. */
    public function obtener(string $id): Cancion
    {
        return $this->repositorio->buscarPorId($id)
            ?? throw new CancionNoEncontrada($id);
    }

    /** @return list<Cancion> */
    public function listar(): array
    {
        return $this->repositorio->todas();
    }

    /** @param array<string, mixed> $datos */
    public function crear(array $datos): Cancion
    {
        $cancion = Cancion::desdeArray(['id' => $this->repositorio->nuevoId()] + $datos);
        $this->repositorio->guardar($cancion);

        return $cancion;
    }

    /** @param array<string, mixed> $datos */
    public function actualizar(string $id, array $datos): Cancion
    {
        $this->obtener($id);   // reutiliza la regla del 404

        $cancion = Cancion::desdeArray(['id' => $id] + $datos);
        $this->repositorio->guardar($cancion);

        return $cancion;
    }

    public function eliminar(string $id): void
    {
        $this->obtener($id);
        $this->repositorio->eliminar($id);
    }
}
