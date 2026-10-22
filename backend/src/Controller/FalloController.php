<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Json;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class FalloController
{
    /** @param array<string, string> $args */
    public function boom(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        throw new \RuntimeException('Explotó a propósito');
    }

    /** @param array<string, string> $args */
    public function aviso(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        $vacio = [];
        $valor = $vacio['no_existe']; // E_WARNING -> ErrorException -> 500

        return Json::respuesta(200, ['ok' => true, 'valor' => $valor]);
    }

    /** @param array<string, string> $args */
    public function fatal(ServerRequestInterface $peticion, array $args): ResponseInterface
    {
        trigger_error('Fatal controlado', E_USER_ERROR); // -> shutdown -> 500

        return Json::respuesta(200, ['ok' => true]); // no se alcanza
    }
}
