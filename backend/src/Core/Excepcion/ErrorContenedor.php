<?php

declare(strict_types=1);

namespace App\Core\Excepcion;

use Psr\Container\ContainerExceptionInterface;

final class ErrorContenedor extends \RuntimeException implements ContainerExceptionInterface
{
}
