import { Tarjeta } from '../../components/Tarjeta.tsx';
import type { Cancion } from '../../api/tipos.ts';

const PROVISIONALES: Cancion[] = [
  {
    id: '1',
    titulo: 'La llorona',
    artista: 'Tradicional',
    tono: 'Am',
    versos: ['Salías del templo un día, llorona', 'cuando al pasar yo te vi'],
  },
  {
    id: '2',
    titulo: 'Cielito lindo',
    artista: 'Quirino Mendoza',
    tono: 'C',
    versos: ['De la sierra morena, cielito lindo', 'vienen bajando'],
  },
  {
    id: '3',
    titulo: 'La bamba',
    artista: 'Tradicional',
    tono: 'D',
    versos: ['Para bailar la bamba', 'se necesita una poca de gracia'],
  },
];

export function Baraja() {
  return (
    <main className="baraja">
      {PROVISIONALES.map((cancion) => (
        <Tarjeta key={cancion.id} cancion={cancion} />
      ))}
    </main>
  );
}
