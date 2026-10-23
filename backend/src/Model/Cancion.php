<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Modelo de dominio: una canción. Inmutable (readonly), sin lógica de
 * persistencia ni de HTTP. El contrato de "qué es una canción" vive aquí.
 */
final readonly class Cancion
{
    /** @param list<string> $versos */
    public function __construct(
        public string $id,
        public string $titulo,
        public string $artista,
        public string $tono,
        public array $versos,
    ) {
    }

    /**
     * Hidratación: array crudo (Firestore) -> objeto tipado.
     * Tolerante a campos ausentes a propósito: el punto único de traducción.
     *
     * @param array<string, mixed> $datos
     */
    public static function desdeArray(array $datos): self
    {
        return new self(
            (string) ($datos['id'] ?? ''),
            (string) ($datos['titulo'] ?? 'Sin título'),
            (string) ($datos['artista'] ?? 'Desconocido'),
            (string) ($datos['tono'] ?? ''),
            array_values(array_map('strval', $datos['versos'] ?? [])),
        );
    }

    /** @return array<string, mixed> */
    public function aArray(): array
    {
        return [
            'id'      => $this->id,
            'titulo'  => $this->titulo,
            'artista' => $this->artista,
            'tono'    => $this->tono,
            'versos'  => $this->versos,
        ];
    }
}
