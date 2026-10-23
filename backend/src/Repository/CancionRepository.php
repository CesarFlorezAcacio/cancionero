<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Cancion;

/**
 * Contrato de acceso a datos. Quien lo use (el servicio) no sabe si detrás
 * hay memoria, Firestore o un fichero.
 */
interface CancionRepository
{
    public function buscarPorId(string $id): ?Cancion;

    /** @return list<Cancion> */
    public function todas(): array;

    public function guardar(Cancion $cancion): void;
}
