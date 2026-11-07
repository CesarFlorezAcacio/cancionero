<?php

declare(strict_types=1);

namespace App\Service\Validacion;

use App\Excepcion\ErrorValidacion;

/** Una sola definición de "canción válida", compartida por crear y actualizar. */
final class ValidadorCancion
{
    private const MAX_TITULO  = 120;
    private const MAX_ARTISTA = 120;
    private const MAX_VERSOS  = 200;

    /**
     * @param  array<string, mixed> $datos
     * @return array<string, mixed> datos normalizados
     */
    public function validar(array $datos): array
    {
        $errores = [];

        $titulo = trim((string) ($datos['titulo'] ?? ''));
        if ($titulo === '') {
            $errores['titulo'] = 'El título es obligatorio.';
        } elseif (mb_strlen($titulo) > self::MAX_TITULO) {
            $errores['titulo'] = 'Máximo ' . self::MAX_TITULO . ' caracteres.';
        }

        $artista = trim((string) ($datos['artista'] ?? 'Desconocido'));
        if (mb_strlen($artista) > self::MAX_ARTISTA) {
            $errores['artista'] = 'Máximo ' . self::MAX_ARTISTA . ' caracteres.';
        }

        $tono = trim((string) ($datos['tono'] ?? ''));

        $versos = $datos['versos'] ?? null;
        if (!is_array($versos) || $versos === []) {
            $errores['versos'] = 'Se necesita al menos un verso.';
            $versos = [];
        } elseif (count($versos) > self::MAX_VERSOS) {
            $errores['versos'] = 'Máximo ' . self::MAX_VERSOS . ' versos.';
        } else {
            $limpios = [];
            foreach ($versos as $i => $verso) {
                $texto = trim((string) $verso);
                if ($texto === '') {
                    $errores["versos.{$i}"] = 'El verso no puede estar vacío.';
                    continue;
                }
                $limpios[] = $texto;
            }
            $versos = $limpios;
        }

        if ($errores !== []) {
            throw new ErrorValidacion($errores);
        }

        return ['titulo' => $titulo, 'artista' => $artista, 'tono' => $tono, 'versos' => $versos];
    }
}
