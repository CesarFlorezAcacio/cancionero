<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Cancion;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Misma interfaz que la versión en memoria: el servicio no nota la diferencia.
 * Aquí vive todo el detalle de Firestore.
 */
final class CancionRepositoryFirestore implements CancionRepository
{
    private const COLECCION = 'canciones';

    public function __construct(private FirestoreClient $firestore)
    {
    }

    private function coleccion(): CollectionReference
    {
        return $this->firestore->collection(self::COLECCION);
    }

    public function buscarPorId(string $id): ?Cancion
    {
        $documento = $this->coleccion()->document($id)->snapshot();

        if (!$documento->exists()) {
            return null;
        }

        // El id vive en la ruta: se reinyecta al hidratar.
        return Cancion::desdeArray(['id' => $id] + $documento->data());
    }

    /** @return list<Cancion> */
    public function todas(): array
    {
        $canciones = [];

        foreach ($this->coleccion()->documents() as $documento) {
            $canciones[] = Cancion::desdeArray(['id' => $documento->id()] + $documento->data());
        }

        return $canciones;
    }

    public function guardar(Cancion $cancion): void
    {
        $datos = $cancion->aArray();
        unset($datos['id']);   // el id ya está en la ruta del documento

        $this->coleccion()->document($cancion->id)->set($datos);
    }

    public function eliminar(string $id): void
    {
        $this->coleccion()->document($id)->delete();
    }

    public function nuevoId(): string
    {
        return $this->coleccion()->newDocument()->id();
    }
}
