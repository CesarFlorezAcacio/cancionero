<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Emisor;
use App\Http\Json;
use Psr\Http\Message\ResponseInterface;

/**
 * Red de seguridad: avisos -> excepciones; excepciones y fatales -> JSON.
 * PHP nunca imprime por su cuenta (display_errors = 0).
 */
final class ManejadorErrores
{
    private static bool $fatalesRegistrados = false;
    private static bool $emitido = false;

    public static function registrar(): void
    {
        self::$emitido = false;                      // abre petición, como un proceso fpm limpio

        set_error_handler([self::class, 'avisos']);
        set_exception_handler([self::class, 'excepciones']);

        if (!self::$fatalesRegistrados) {            // el shutdown sí es único
            self::$fatalesRegistrados = true;
            register_shutdown_function([self::class, 'fatales']);
        }
    }

    public static function avisos(int $nivel, string $mensaje, string $archivo, int $linea): bool
    {
        if ((error_reporting() & $nivel) === 0) {
            return false;
        }
        // E_USER_ERROR se deja ser fatal para que lo recoja el shutdown.
        if ($nivel === E_USER_ERROR) {
            return false;
        }

        throw new \ErrorException($mensaje, 0, $nivel, $archivo, $linea);
    }

    public static function excepciones(\Throwable $excepcion): void
    {
        self::$emitido = true;
        Emisor::enviar(self::json($excepcion));
    }

    public static function fatales(): void
    {
        $error = error_get_last();

        if (self::$emitido || $error === null
            || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
            return;
        }

        // Limpia salida parcial (buffer abierto en index.php) antes de emitir.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        self::$emitido = true;
        Emisor::enviar(self::json(new \ErrorException(
            $error['message'],
            0,
            $error['type'],
            $error['file'],
            $error['line'],
        )));
    }

    private static function json(\Throwable $excepcion): ResponseInterface
    {
        return Json::respuesta(500, [
            'ok'      => false,
            'error'   => 'Error fatal',
            'mensaje' => $excepcion->getMessage(),
            'origen'  => $excepcion->getFile() . ':' . $excepcion->getLine(),
        ]);
    }

    public static function reset(): void
    {
        self::$emitido = false;
    }

    public static function restaurar(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }
}
