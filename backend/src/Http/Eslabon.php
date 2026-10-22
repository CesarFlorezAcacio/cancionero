<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** Un eslabón de la cadena: envuelve a un middleware con el siguiente handler. */
final class Eslabon implements RequestHandlerInterface
{
    public function __construct(
        private MiddlewareInterface $middleware,
        private RequestHandlerInterface $siguiente,
    ) {
    }

    public function handle(ServerRequestInterface $peticion): ResponseInterface
    {
        return $this->middleware->process($peticion, $this->siguiente);
    }
}
