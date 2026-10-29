<?php

declare(strict_types=1);

namespace App\Excepcion;

final class CuerpoMalFormado extends \RuntimeException implements ErrorHttp
{
    public function __construct()
    {
        parent::__construct('El cuerpo no es JSON válido.');
    }

    public function estado(): int { return 400; }
    public function datos(): array { return []; }
}
