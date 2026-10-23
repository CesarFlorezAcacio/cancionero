<?php

declare(strict_types=1);

namespace App\Excepcion;

final class CancionNoEncontrada extends \RuntimeException implements ErrorHttp
{
    public function __construct(string $id)
    {
        parent::__construct("No existe la canción '{$id}'.");
    }

    public function estado(): int
    {
        return 404;
    }
}
