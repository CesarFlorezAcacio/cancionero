<?php

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/../../vendor/autoload.php';

use App\Core\Aplicacion;
use Nyholm\Psr7\ServerRequest;

Aplicacion::crear()->procesar(new ServerRequest('GET', '/api/fatal'));
