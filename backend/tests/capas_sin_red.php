<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Excepcion\CancionNoEncontrada;
use App\Model\Cancion;
use App\Repository\CancionRepository;
use App\Service\CancionService;

$fallos = 0;

function comprobar(string $nombre, mixed $obtenido, mixed $esperado): void
{
    global $fallos;
    $ok = $obtenido === $esperado;
    printf("%s\t%s\n", $ok ? 'OK' : 'FALLA', $nombre);
    if (!$ok) {
        printf("\tesperado: %s | obtenido: %s\n", var_export($esperado, true), var_export($obtenido, true));
        $fallos++;
    }
}

/** Implementación falsa: cero red, cero Firestore. */
final class RepoFalso implements CancionRepository
{
    /** @var array<string, Cancion> */
    private array $datos = [];

    public function __construct()
    {
        $this->datos['1'] = new Cancion('1', 'Titulo falso', 'Artista falso', 'C', ['verso uno']);
    }

    public function buscarPorId(string $id): ?Cancion
    {
        return $this->datos[$id] ?? null;
    }

    /** @return list<Cancion> */
    public function todas(): array
    {
        return array_values($this->datos);
    }

    public function guardar(Cancion $cancion): void
    {
        $this->datos[$cancion->id] = $cancion;
    }
}

// El servicio se construye a mano con una implementación DISTINTA a la de producción.
$servicio = new CancionService(new RepoFalso());

comprobar('servicio devuelve el modelo', $servicio->obtener('1')->titulo, 'Titulo falso');
comprobar('servicio devuelve tipo Cancion', $servicio->obtener('1') instanceof Cancion, true);
comprobar('listar devuelve 1', count($servicio->listar()), 1);

try {
    $servicio->obtener('999');
    comprobar('id inexistente lanza CancionNoEncontrada', false, true);
} catch (CancionNoEncontrada $excepcion) {
    comprobar('id inexistente lanza CancionNoEncontrada', true, true);
    comprobar('y su status es 404', $excepcion->estado(), 404);
}

// Hidratación tolerante
$parcial = Cancion::desdeArray(['id' => 'x']);
comprobar('hidratacion tolera campos ausentes', $parcial->artista, 'Desconocido');

exit($fallos === 0 ? 0 : 1);
