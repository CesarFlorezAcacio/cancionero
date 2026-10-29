<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Model\Cancion;
use App\Repository\CancionRepository;

final class RepoFalso implements CancionRepository
{
    /** @var array<string, Cancion> */
    private array $datos = [];
    private int $siguiente = 100;

    public function __construct()
    {
        $this->datos['1'] = new Cancion('1', 'Titulo falso', 'Artista falso', 'C', ['verso uno']);
    }

    public function buscarPorId(string $id): ?Cancion { return $this->datos[$id] ?? null; }
    public function todas(): array { return array_values($this->datos); }
    public function pagina(int $limite, ?string $despuesDe): array { return array_slice($this->todas(), 0, $limite); }
    public function guardar(Cancion $cancion): void { $this->datos[$cancion->id] = $cancion; }
    public function eliminar(string $id): void { unset($this->datos[$id]); }
    public function nuevoId(): string { return (string) $this->siguiente++; }
}
