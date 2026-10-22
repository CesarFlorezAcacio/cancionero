<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$clases = [
    'App\Core\Kernel',
    'App\Http\Json',
    'App\Controller\HolaController',
    'Psr\Http\Message\ResponseInterface',
    'Symfony\Component\VarDumper\VarDumper',
];

$fallos = 0;

foreach ($clases as $clase) {
    $existe = class_exists($clase) || interface_exists($clase);
    printf("%s  %s\n", $existe ? 'OK   ' : 'FALLA', $clase);
    $fallos += $existe ? 0 : 1;
}

exit($fallos === 0 ? 0 : 1);
