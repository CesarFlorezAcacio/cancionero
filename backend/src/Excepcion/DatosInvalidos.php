<?php

declare(strict_types=1);

namespace App\Excepcion;

/** Errores de validación acumulados por campo. */
final class DatosInvalidos extends \InvalidArgumentException implements ErrorHttp
{
    /** @param array<string, string> $errores */
    public function __construct(private array $errores)
    {
        parent::__construct('Los datos enviados no son válidos.');
    }

    /** @return array<string, string> */
    public function errores(): array
    {
        return $this->errores;
    }

    public function estado(): int
    {
        return 400;
    }
}
