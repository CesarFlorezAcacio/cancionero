<?php

declare(strict_types=1);

namespace App\Excepcion;

use Throwable;

/** Un error de dominio que sabe con qué status HTTP debe salir. */
interface ErrorHttp extends Throwable
{
    public function estado(): int;
}
