export interface CancionBruta {
  id: number;
  titulo: string;
  artista: string;
  tono: string;
  versos: string[];
}

export interface PaginaBruta {
  siguiente: string | null;
  canciones: CancionBruta[];
}

export type Cancion = {
  id: string;
  titulo: string;
  artista: string;
  tono: string;
  versos: string[];
};

export type PaginaCanciones = {
  ok: true;
  canciones: Cancion[];
  siguiente: string | null;
};
