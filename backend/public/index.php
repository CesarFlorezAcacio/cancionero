<?php

declare(strict_types=1);

// Front controller mínimo del Día 3.
// Todas las peticiones /api/* acaban aquí. Hoy enruta a mano.

$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

header('Content-Type: application/json; charset=utf-8');

if ($ruta === '/api/hola') {
    echo json_encode([
        'ok'      => true,
        'mensaje' => 'Hola desde PHP-FPM',
        'ruta'    => $ruta,
        'hora'    => (new DateTimeImmutable())->format(DATE_ATOM),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(404);
echo json_encode([
    'ok'    => false,
    'error' => 'Ruta no encontrada',
    'ruta'  => $ruta,
], JSON_UNESCAPED_UNICODE);
