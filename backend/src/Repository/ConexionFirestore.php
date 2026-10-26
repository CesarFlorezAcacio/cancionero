<?php

declare(strict_types=1);

namespace App\Repository;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;

/** Único sitio que sabe cómo se abre la conexión a Firestore. */
final class ConexionFirestore
{
    public static function crear(): FirestoreClient
    {
        $factory = new Factory();

        $credenciales = getenv('FIREBASE_CREDENTIALS');
        if ($credenciales !== false && $credenciales !== '' && is_file($credenciales)) {
            $factory = $factory->withServiceAccount($credenciales);
        }

        $proyecto = getenv('FIRESTORE_PROJECT_ID');
        if ($proyecto !== false && $proyecto !== '') {
            $factory = $factory->withProjectId($proyecto);
        }

        // Con FIRESTORE_EMULATOR_HOST definido, el cliente va al emulador y no pide credenciales.
        return $factory->createFirestore()->database();
    }
}
