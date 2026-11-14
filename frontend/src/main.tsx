import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { PruebaApi } from './features/cards/PruebaApi.tsx';
import './estilos.css';

const raiz = document.getElementById('raiz');
if (!raiz) throw new Error('Falta el nodo #raiz');

createRoot(raiz).render(
  <StrictMode>
    <PruebaApi />
  </StrictMode>,
);
