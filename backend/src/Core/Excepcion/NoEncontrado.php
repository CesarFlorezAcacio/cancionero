<?php

declare(strict_types=1);

namespace App\Core\Excepcion;

use Psr\Container\NotFoundExceptionInterface;

final class NoEncontrado extends \RuntimeException implements NotFoundExceptionInterface
{
}
