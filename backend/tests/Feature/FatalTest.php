<?php

declare(strict_types=1);

use App\Core\Aplicacion;
use Nyholm\Psr7\ServerRequest;

it('convierte un aviso fatal en 500', function (): void {
    $salida = shell_exec('php ' . escapeshellarg(__DIR__ . '/../Scripts/fatal.php') . ' 2>&1');

    expect($salida)->toContain('"error":"Error fatal"');
})->group('fatal');
