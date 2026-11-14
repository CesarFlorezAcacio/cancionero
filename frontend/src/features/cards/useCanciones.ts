import { useEffect, useReducer, useState } from 'react';
import { ApiError } from '../../api/http';
import { pedirCanciones } from '../../api/canciones';
import type { Cancion } from '../../api/types';

export type EstadoCanciones =
  | { fase: 'inicial' }
  | { fase: 'cargando' }
  | { fase: 'listo'; canciones: Cancion[]; siguiente: string | null }
  | { fase: 'error'; error: ApiError };

type Accion =
  | { tipo: 'cargar' }
  | { tipo: 'exito'; canciones: Cancion[]; siguiente: string | null }
  | { tipo: 'fallo'; error: ApiError };

function reducir(_estado: EstadoCanciones, accion: Accion): EstadoCanciones {
  switch (accion.tipo) {
    case 'cargar':
      return { fase: 'cargando' };
    case 'exito':
      return { fase: 'listo', canciones: accion.canciones, siguiente: accion.siguiente };
    case 'fallo':
      return { fase: 'error', error: accion.error };
  }
}

export function useCanciones() {
  const [estado, despachar] = useReducer(reducir, { fase: 'inicial' });
  const [intento, setIntento] = useState(0);

  useEffect(() => {
    const control = new AbortController();
    despachar({ tipo: 'cargar' });

    pedirCanciones(null, control.signal)
      .then((pagina) => {
        despachar({ tipo: 'exito', canciones: pagina.canciones, siguiente: pagina.siguiente });
      })
      .catch((error: unknown) => {
        if (error instanceof ApiError && error.tipo === 'cancelado') return;
        
        despachar({
          tipo: 'fallo',
          error: error instanceof ApiError ? error : new ApiError('red', 'Error inesperado'),
        });
      });

    return () => control.abort();
  }, [intento]);

  return { estado, reintentar: () => setIntento((n) => n + 1) };
}
