<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Model\Cancion;
use App\Repository\CancionRepositoryFirestore;
use App\Repository\ConexionFirestore;

$repositorio = new CancionRepositoryFirestore(ConexionFirestore::crear());

$semilla = [
    new Cancion('1', 'Cielito lindo', 'Quirino Mendoza', 'Do mayor', ['De la sierra morena, cielito lindo, vienen bajando']),
    new Cancion('2', 'La bamba', 'Tradicional', 'Do mayor', ['Para bailar la bamba se necesita una poca de gracia']),
    new Cancion('3', 'Las mañanitas', 'Tradicional', 'Sol mayor', ['Estas son las mañanitas que cantaba el rey David']),
];

foreach ($semilla as $cancion) {
    $repositorio->guardar($cancion);
    echo "sembrada {$cancion->id}\n";
}
