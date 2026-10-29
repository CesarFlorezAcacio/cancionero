<?php

declare(strict_types=1);

namespace App\Service;

use App\Excepcion\DatosInvalidos;

/** Valida y normaliza la entrada cruda. Único sitio donde "una canción válida" se define. */
final class ValidadorCancion
{
    private const TITULO_MAX = 120;
    private const VERSOS_MAX = 200;

    /**
     * @param array<string, mixed> $datos
     * @return array{titulo: string, artista: string, tono: string, versos: list<string>}
     */
    public function validar(array $datos): array
    {
        $errores = [];

        $titulo = trim((string) ($datos['titulo'] ?? ''));
        if (\$tit
