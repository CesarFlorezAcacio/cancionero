import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { Baraja } from './features/cards/Baraja.tsx';
import './estilos.css';

const raiz = document.getElementById('raiz');
if (!raiz) throw new Error('Falta el nodo #raiz');

createRoot(raiz).render(
  <StrictMode>
    <Baraja />
  </StrictMode>,
);
