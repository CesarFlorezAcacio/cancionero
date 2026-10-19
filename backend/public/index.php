<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Kernel;
use App\Http\Emisor;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

// EL BORDE: el mundo exterior se lee UNA vez y se convierte en objeto.
$psr17 = new Psr17Factory();

$peticion = (new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals();

// Dentro, solo objetos. Y al salir, se vuelca al exterior.
Emisor::enviar((new Kernel())->handle($peticion));
