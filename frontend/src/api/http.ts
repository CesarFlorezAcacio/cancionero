const BASE = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '') ;
const TIMEOUT_MS = 8000;

export type TipoError = 'red' | 'http' | 'formato' | 'tiempo' | 'cancelado';

export class ApiError extends Error {
  readonly tipo: TipoError;
  readonly status?: number;

  constructor(tipo: TipoError, mensaje: string, status?: number) {
    super(mensaje);
    this.name = 'ApiError';
    this.tipo = tipo;
    this.status = status;
  }
}

export async function pedirJson<T>(ruta: string, senal?: AbortSignal): Promise<T> {
  const control = new AbortController();
  let porTimeout = false;

  const temporizador = setTimeout(() => {
    porTimeout = true;
    control.abort();
  }, TIMEOUT_MS);

  const senalFinal = senal ? AbortSignal.any([senal, control.signal]) : control.signal;

  try {
    const respuesta = await fetch(`${BASE}${ruta}`, {
      signal: senalFinal,
      headers: { Accept: 'application/json' },
    });

    if (!respuesta.ok) {
      throw new ApiError('http', `El servidor respondió ${respuesta.status}`, respuesta.status);
    }

    try {
      return (await respuesta.json()) as T;
    } catch {
      throw new ApiError('formato', 'La respuesta no es JSON válido');
    }
  } catch (error) {
    if (error instanceof ApiError) throw error;
    if (error instanceof DOMException && error.name === 'AbortError') {
      throw porTimeout
        ? new ApiError('tiempo', 'La petición tardó demasiado')
        : new ApiError('cancelado', 'Petición cancelada');
    }
    throw new ApiError('red', 'No se pudo contactar con el servidor');
  } finally {
    clearTimeout(temporizador);
  }
}
