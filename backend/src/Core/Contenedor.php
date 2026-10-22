<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Excepcion\ErrorContenedor;
use App\Core\Excepcion\NoEncontrado;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Contenedor de dependencias mínimo, compatible con PSR-11.
 *
 * Modo 1 (explícito): set(id, fabrica) -> manda siempre.
 * Modo 2 (autowiring): sin definición, construye por reflexión
 *                      resolviendo recursivamente las dependencias tipadas.
 *
 * Devuelve la MISMA instancia por id (singleton por contenedor): construcción perezosa.
 */
final class Contenedor implements ContainerInterface
{
    /** @var array<string, callable(self): object> */
    private array $definiciones = [];

    /** @var array<string, object> */
    private array $instancias = [];

    /** @param callable(self): object $fabrica */
    public function set(string $id, callable $fabrica): void
    {
        $this->definiciones[$id] = $fabrica;
    }

    public function has(string $id): bool
    {
        return isset($this->definiciones[$id]) || class_exists($id);
    }

    public function get(string $id): object
    {
        if (isset($this->instancias[$id])) {
            return $this->instancias[$id];
        }

        $objeto = isset($this->definiciones[$id])
            ? ($this->definiciones[$id])($this)
            : $this->autoinyectar($id);

        return $this->instancias[$id] = $objeto;
    }

    private function autoinyectar(string $clase): object
    {
        if (!class_exists($clase)) {
            throw new NoEncontrado("Sin definición ni clase para '{$clase}'.");
        }

        $reflexion = new ReflectionClass($clase);

        if (!$reflexion->isInstantiable()) {
            throw new NoEncontrado("'{$clase}' no es instanciable (¿interfaz o abstracta sin definir?).");
        }

        $constructor = $reflexion->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflexion->newInstance();
        }

        $argumentos = [];
        foreach ($constructor->getParameters() as $parametro) {
            $tipo = $parametro->getType();

            // Sin tipo o tipo nativo: no autowireable, tira del valor por defecto.
            if (!$tipo instanceof ReflectionNamedType || $tipo->isBuiltin()) {
                if ($parametro->isDefaultValueAvailable()) {
                    $argumentos[] = $parametro->getDefaultValue();
                    continue;
                }
                throw new ErrorContenedor(
                    "No resuelvo '{$parametro->getName()}' de '{$clase}': tipo nativo sin valor por defecto."
                );
            }

            $argumentos[] = $this->get($tipo->getName());
        }

        return $reflexion->newInstanceArgs($argumentos);
    }
}
