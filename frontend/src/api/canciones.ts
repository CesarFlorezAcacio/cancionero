import { ApiError, pedirJson } from './http';
import type { CancionBruta, PaginaBruta, Cancion, PaginaCanciones } from './types';

function normalizarCancion(bruto: CancionBruta, indice: number): Cancion {
  const titulo = bruto.titulo.trim();
  if (titulo === '') {
    throw new ApiError('formato', `Canción sin título en la posición ${indice}`);
  }
  if (!Array.isArray(bruto.versos) || bruto.versos.some(verso => typeof verso !== "string")) {
    throw new ApiError('formato', `Formato de verso no válido en la posición ${indice}`);
  }
  return {
    id: bruto.id,
    titulo,
    artista: bruto.artista.trim(),
    tono: bruto.tono.trim(),
    versos: bruto.versos.map((v) => v.trim()).filter((v) => v !== ''),
  };
}


export async function pedirCanciones(cursor?: string, senal?: AbortSignal): Promise<PaginaCanciones> {
  const url = cursor
    ? `/canciones?cursor=${encodeURIComponent(cursor)}`
    : `/canciones`;

  const bruto = await pedirJson<PaginaBruta>(url, senal);

  return {
    siguiente: bruto.siguiente,
    canciones: bruto.canciones.map((c, i) => normalizarCancion(c, i)),
  };
}
