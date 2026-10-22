<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Cors implements MiddlewareInterface
{
    public function process(ServerRequestInterface $peticion, RequestHandlerInterface $manejador): ResponseInterface
    {
        // Preflight: se responde sin molestar al núcleo.
        $respuesta = $peticion->getMethod() === 'OPTIONS'
            ? (new Psr17Factory())->createResponse(204)
            : $manejador->handle($peticion);

        return $respuesta
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
