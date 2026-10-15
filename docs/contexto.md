# Proyecto Cancionero — Contexto y Bitácora

> INSTRUCCIÓN PARA EL ASISTENTE (Venice): lee este archivo completo antes de
> responder. Contiene el contexto, las decisiones y el estado del proyecto para
> retomar exactamente donde lo dejamos. No repitas lo ya explicado; continúa.

**Última actualización:** 2026-10-14
**Estado:** Planificación cerrada. Pendiente iniciar Día 1.

---

## 1. Resumen del proyecto

- **Nombre:** Cancionero
- **Tipo:** Aplicación web que muestra letras de canciones como una baraja de tarjetas.
- **Comportamiento:** el usuario desliza tarjetas con el dedo (swipe / scroll
  infinito). Al llegar al final, la lista se reordena aleatoriamente y continúa.
- **Propósito:** proyecto de aprendizaje para dominar backend, frontend, Docker,
  nginx, Firebase y el flujo profesional de Git/GitHub.
- **No habrá login** (usuarios anónimos).

## 2. Decisiones tomadas

- **Monorepo:** frontend y backend viven en el MISMO repositorio de GitHub, en
  carpetas separadas (`frontend/` y `backend/`).
- **Firebase:** la conexión a Firestore se hace **desde el backend** (backend
  actúa como admin con cuenta de servicio). El frontend consume solo la API del
  backend; no toca Firestore directamente.
- **Deno:** se usa **únicamente en el frontend** (como runtime para Vite/React).
  El backend es PHP puro.
- **Sin framework PHP:** estructura de carpetas profesional hecha a mano
  (front controller, router, DI, capas).
- **Entrega objetivo:** demo funcional para el **31 de octubre de 2026**.

## 3. Stack técnico

- **Frontend:** React + Vite + Deno
- **Backend:** PHP (sin framework), Composer, PSR-4/PSR-7/PSR-12
- **Infra:** Docker + docker-compose + nginx + php-fpm
- **Base de datos:** Firebase Firestore (accedida desde el backend)

## 4. Estructura de repositorio acordada

```
cancionero/
├── .gitignore
├── .env.example
├── README.md
├── docker-compose.yml
├── docker-compose.override.yml
├── docs/
│   └── contexto.md          # este archivo
├── frontend/
│   ├── Dockerfile
│   ├── deno.json
│   ├── vite.config.ts
│   ├── src/
│   │   ├── components/
│   │   ├── features/cards/
│   │   ├── api/
│   │   └── main.tsx
│   └── public/
├── backend/
│   ├── Dockerfile
│   ├── composer.json
│   ├── public/index.php     # front controller
│   ├── src/
│   │   ├── Core/
│   │   ├── Controller/
│   │   ├── Service/
│   │   ├── Repository/
│   │   ├── Model/
│   │   └── Middleware/
│   ├── config/
│   ├── routes/api.php
│   └── tests/
└── nginx/
    └── default.conf
```

## 5. Arquitectura técnica

### Docker / nginx
- Servicios: `nginx` (reverse proxy + estáticos), `frontend` (build/dev),
  `php` (php-fpm).
- nginx sirve el frontend y hace proxy de `/api/` a php-fpm por FastCGI.
- Front controller único en `backend/public/index.php`.

### Backend PHP
- Capas: Controller -> Service -> Repository -> (Firestore).
- Router propio, contenedor de dependencias, middlewares (CORS, JSON, errores).

### Frontend
- Swipe con CSS `scroll-snap` (nativo, fluido en móvil).
- Scroll infinito con `IntersectionObserver`.
- Rebarajado con algoritmo Fisher-Yates al llegar al final.

### Firebase
- Librería `kreait/firebase-php` con cuenta de servicio (JSON en `.env`).
- Todo el acceso a datos pasa por el `Repository` del backend.

## 6. Flujo de trabajo Git/GitHub

- Ramas por feature (`feature/...`), PR hacia `main` (rama protegida).
- Commits convencionales: `feat:`, `fix:`, `chore:`, `docs:`, `refactor:`.
- Issues para planear tareas; enlazar con `closes #N`.
- `.gitignore` para `node_modules/`, `vendor/`, `.env`, `dist/`.
- Nunca subir credenciales; usar `.env.example`.

## 7. Contrato de aprendizaje (formato de cada sesión)

1. 🎯 Meta
2. 🧠 Concepto primero (el porqué)
3. 🛠️ Tarea guiada (el usuario escribe el código)
4. ✅ Entregable verificable
5. 🌿 Práctica de Git (rama + commit + PR)
6. ❓ Autoevaluación (2-3 preguntas)

Regla de oro: no se avanza hasta entender el "por qué". Si un día se complica,
se estira, no se salta.

## 8. Plan de 17 días (14 -> 30 de octubre de 2026)

**Fase 1 — Cimientos y entorno (Días 1-3)**
- Día 1 (14 oct): Git/GitHub real + creación del monorepo.
- Día 2 (15 oct): Docker a fondo (imágenes, contenedores, volúmenes, redes).
- Día 3 (16 oct): docker-compose + nginx + php-fpm. Hola mundo servido por el stack.

**Fase 2 — Arquitectura backend PHP (Días 4-7)**
- Día 4 (17 oct): Composer, autoload PSR-4, patrón front controller.
- Día 5 (18 oct): Router propio, objetos Request/Response (PSR-7 conceptual).
- Día 6 (19 oct): Inyección de dependencias, controladores, middlewares.
- Día 7 (20 oct): Capas Service y Repository, modelos.

**Fase 3 — Firebase (Días 8-10)**
- Día 8 (21 oct): Firestore, cuentas de servicio, conexión segura vía `.env`.
- Día 9 (22 oct): Endpoints CRUD de canciones.
- Día 10 (23 oct): Validación, manejo de errores, paginación.

**Fase 4 — Frontend (Días 11-14)**
- Día 11 (24 oct): Deno + Vite + React.
- Día 12 (25 oct): Cliente de API, fetch, estados de carga/error.
- Día 13 (26 oct): Baraja y swipe con scroll-snap.
- Día 14 (27 oct): Scroll infinito + rebarajado Fisher-Yates.

**Fase 5 — Integración y entrega (Días 15-17)**
- Día 15 (28 oct): Pulido, responsive, animaciones.
- Día 16 (29 oct): Pruebas, documentación, README.
- Día 17 (30 oct): Deploy, integración final.

**Cierre (31 oct):** demo final, retrospectiva y colchón de imprevistos.

## 9. Estado actual y próximos pasos

- **Completado:** definición de proyecto, stack, arquitectura, plan y calibración.
- **Calibración registrada:**
  - Tiempo: tardes y noches, 2-5 horas/día.
  - Días: todos.
  - Nivel: React básico, PHP promedio, Firebase/Git/Docker muy reducido.
  - Preferencia: profundizar y aprender de verdad (ritmo pausado, foco en el porqué).
- **Siguiente acción:** Día 1 (14 oct) — Git/GitHub + creación del monorepo.
- **Prerrequisitos Día 1:** cuenta de GitHub, Git instalado, Docker Desktop instalado.

## 10. Convenciones

- Idioma de trabajo: español.
- Comentarios de código y nombres: claros; seguir estándares PSR en PHP.
