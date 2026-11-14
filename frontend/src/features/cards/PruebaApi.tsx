import { useCanciones } from './useCanciones';

function mensajeDe(error: { tipo: string; status?: number }): string {
  switch (error.tipo) {
    case 'red':
      return 'Sin conexión con el servidor. Revisa que el stack esté levantado.';
    case 'tiempo':
      return 'El servidor tardó demasiado en responder.';
    case 'http':
      return error.status === 404
        ? 'El recurso no existe.'
        : `El servidor falló (${error.status}).`;
    case 'formato':
      return 'La respuesta llegó en un formato que no esperábamos.';
    default:
      return 'Algo salió mal.';
  }
}

export function PruebaApi() {
  const { estado, reintentar } = useCanciones();

  switch (estado.fase) {
    case 'inicial':
    case 'cargando':
      return <p>Cargando canciones…</p>;

    case 'error':
      const puedeReintentar = estado.error.tipo !== 'http' || (estado.error.status ?? 0) >= 500;
      
      return (
        <div role="alert">
          <p>{mensajeDe(estado.error)}</p>
          { puedeReintentar && <button onClick={reintentar}>Reintentar</button> }
        </div>
      );

    case 'listo':
      return (
        <ul>
          {estado.canciones.map((c) => (
            <li key={c.id}>
              <strong>{c.titulo}</strong> — {c.artista} ({c.tono})
            </li>
          ))}
        </ul>
      );
  }
}
