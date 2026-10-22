<?php

declare(strict_types=1);

use App\Core\Aplicacion;
use App\Http\Emisor;

// El API responde JSON: PHP nunca imprime por su cuenta.
ini_set('display_errors', '0');
error_reporting(E_ALL);
ob_start(); // captura salida prematura para poder limpiarla ante un fatal

require __DIR__ . '/../vendor/autoload.php';

Emisor::enviar(Aplicacion::crear()->procesarDesdeGlobals());
