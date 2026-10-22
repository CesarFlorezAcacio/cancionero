<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Cadena de middlewares (modelo cebolla, PSR-15).
 * El PRIMER middleware añadido es el MÁS EXTERNO.
 */
final class Tuberia
{
    /** @var list<MiddlewareInterface> */
    private array $middlewares = [];

    public function __construct(private RequestHandlerInterface $nucleo)
    {
    }

    public function agregar(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function procesar(ServerRequestInterface $peticion): ResponseInterface
    {
        // Se arma de dentro hacia fuera; el último reduce queda como más externo.
        $cadena = array_reduce(
            array_reverse($this->middlewares),
            static fn (RequestHandlerInterface $siguiente, MiddlewareInterface $m): RequestHandlerInterface
                => new Eslabon($m, $siguiente),
            $this->nucleo,
        );

        return $cadena->handle($peticion);
    }
}
