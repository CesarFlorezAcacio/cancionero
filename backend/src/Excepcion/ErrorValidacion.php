<?php

declare(strict_types=1);

namespace App\Excepcion;

final class ErrorValidacion extends \RuntimeException implements ErrorHttp
{
    /** @param array<string, string> $errores */
    public function __construct(private array $errores)
    {
        parent::__construct('Los datos enviados no son válidos.');
    }

    public function estado(): int { return 422; }

    public function datos(): array { return ['errores' => $this->errores]; }
}
