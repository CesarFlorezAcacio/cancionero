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
}
