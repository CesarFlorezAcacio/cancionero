<?php

declare(strict_types=1);

// Front controller: ÚNICO punto de entrada de la API.
// Este es el único `require` de todo el proyecto. Lo demás se autoloada.

require __DIR__ . '/../vendor/autoload.php';

(new App\Core\Kernel())->run();
