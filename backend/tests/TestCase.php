<?php

declare(strict_types=1);

namespace App\Tests;

use App\Http\Middleware\ManejadorErrores;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // El proceso se reutiliza entre pruebas: los handlers globales hay que limpiarlos.
        ManejadorErrores::reset();
    }
}
