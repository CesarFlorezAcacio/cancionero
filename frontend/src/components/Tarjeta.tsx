import type { Cancion } from '../api/tipos.ts';

export function Tarjeta({ cancion }: { cancion: Cancion }) {
  return (
    <article className="tarjeta">
      <h2>{cancion.titulo}</h2>
      <p className="artista">
        {cancion.artista}
        {cancion.tono ? ` · ${cancion.tono}` : ''}
      </p>
      <div className="versos">
        {cancion.versos.map((verso, i) => (
          <p key={i}>{verso}</p>
        ))}
      </div>
    </article>
  );
}
