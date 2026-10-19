<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Volcado de una Response PSR-7 a la SAPI de PHP.
 * Es el espejo del borde: el mundo de los objetos vuelve a salir al exterior.
 */
final class Emisor
{
    public static function enviar(ResponseInterface $respuesta): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($respuesta->getStatusCode());

        foreach ($respuesta->getHeaders() as $nombre => $valores) {
            foreach ($valores as $valor) {
                // false = añade, no reemplaza: una cabecera puede tener varios valores.
                header($nombre . ': ' . $valor, false);
            }
        }

        $cuerpo = $respuesta->getBody();

        // Gotcha de PSR-7: el stream tiene posición de lectura. Si algo ya lo leyó,
        // getContents() devolvería cadena vacía. Rebobinar siempre.
        if ($cuerpo->isSeekable()) {
            $cuerpo->rewind();
        }

        echo $cuerpo->getContents();
    }
}
