<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Router mínimo: tabla de rutas y compilación de patrones a regex.
 *
 * Sin dependencias de HTTP: recibe strings y devuelve un callable + parámetros.
 * Por eso se puede probar sin levantar nada.
 */
final class Router
{
    /** @var list<array{metodo: string, patron: string, regex: string, controlador: class-string, accion: string}> */
    private array $rutas = [];

    /** @param array{class-string, string} $manejador */
    public function get(string $patron, array $manejador): void
    {
        $this->agregar('GET', $patron, $manejador);
    }

    /** @param array{class-string, string} $manejador */
    public function post(string $patron, array $manejador): void
    {
        $this->agregar('POST', $patron, $manejador);
    }

    /** @param array{class-string, string} $manejador */
    public function put(string $patron, array $manejador): void
    {
        $this->agregar('PUT', $patron, $manejador);
    }

    /** @param array{class-string, string} $manejador */
    public function delete(string $patron, array $manejador): void
    {
        $this->agregar('DELETE', $patron, $manejador);
    }

    /** @param array{class-string, string} $manejador */
    private function agregar(string $metodo, string $patron, array $manejador): void
    {
        [$controlador, $accion] = $manejador;

        $this->rutas[] = [
            'metodo'      => $metodo,
            'patron'      => $patron,
            'regex'       => $this->compilar($patron),
            'controlador' => $controlador,
            'accion'      => $accion,
        ];
    }

    /**
     * Convierte '/api/canciones/{id}' en '#^/api/canciones/(?P<id>[^/]+)$#D'.
     */
    private function compilar(string $patron): string
    {
        // Divide por los comodines; los trozos en posición impar son nombres.
        $trozos = preg_split(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            $patron,
            -1,
            PREG_SPLIT_DELIM_CAPTURE,
        );

        $regex = '';
        foreach ($trozos as $i => $trozo) {
            $regex .= ($i % 2 === 1)
                ? '(?P<' . $trozo . '>[^/]+)'      // nombre de parámetro
                : preg_quote($trozo, '#');          // texto literal, escapado
        }

        // '#' como delimitador; 'D' para que '$' no acepte un \n final.
        return '#^' . $regex . '$#D';
    }

    /** Rutas que hacen match ignorando el método. Para el 405 + Allow. */
    /** @return list<string> */
    public function metodosPermitidos(string $ruta): array
    {
        $metodos = [];
        foreach ($this->rutas as $definida) {
            if (preg_match($definida['regex'], $ruta) === 1) {
                $metodos[] = $definida['metodo'];
            }
        }

        return array_values(array_unique($metodos));
    }

    /** @return array{controlador: class-string, accion: string, parametros: array<string, string>}|null */
    public function resolver(string $metodo, string $ruta): ?array
    {
        foreach ($this->rutas as $definida) {
            if (preg_match($definida['regex'], $ruta, $coincidencias) !== 1) {
                continue;
            }
            if ($definida['metodo'] !== $metodo) {
                continue;
            }

            $parametros = array_filter(
                $coincidencias,
                static fn (string|int $clave): bool => is_string($clave),
                ARRAY_FILTER_USE_KEY,
            );

            return [
                'controlador' => $definida['controlador'],
                'accion'      => $definida['accion'],
                'parametros'  => $parametros,
            ];
        }

        return null;
    }
}
