# Proyecto Cancionero — Contexto y Bitácora

> INSTRUCCIÓN PARA EL ASISTENTE (Venice): lee este archivo completo antes de
> responder. Contiene el contexto, las decisiones, las correcciones y el estado
> del proyecto para retomar exactamente donde lo dejamos. No repitas lo ya
> explicado; continúa desde la "Próxima sesión".

**Última actualización:** 2026-10-15 (cierre de sesión)
**Estado:** Días 1 y 2 completados. Sin incidencias técnicas abiertas.
Quedan tareas de cierre del Día 2 (ver §11).
**Siguiente sesión:** Día 3 — 16 de octubre de 2026.

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
├── docker/
│   └── lab/                 # laboratorio del Día 2 (Dockerfile de práctica)
│       ├── Dockerfile
│       ├── index.html
│       └── .dockerignore
├── docs/
│   ├── contexto.md          # este archivo
│   └── docker-notas.md      # notas propias del Día 2
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
- **Redes:** todos los servicios internos van en una red propia
  (ej. `cancionero_net`). Solo `nginx` publica puertos al host. La base de datos
  y php-fpm no son alcanzables desde fuera.
- **Volúmenes:** código en desarrollo por *bind mount* (recarga en vivo);
  credenciales de Firebase y datos persistentes por *volumen con nombre*.

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
- Squash and merge para dejar la historia de `main` limpia.
- `.gitignore` para `node_modules/`, `vendor/`, `.env`, `dist/`.
- Nunca subir credenciales; usar `.env.example`.

### Regla de oro del flujo (aprendida a golpes el Día 2)
**`git switch -c rama` ANTES de tocar archivos.** Si `git status` dice
`On branch main`, parar y preguntarse si es correcto. Señal de alarma inmediata.

Recuperación ante desastres: `git reflog` guarda ~90 días de historia.
`git branch rescate <hash>` recupera cualquier commit perdido.

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
- ✅ Día 1 (14 oct): Git/GitHub real + creación del monorepo. — **COMPLETADO**
- ✅ Día 2 (15 oct): Docker a fondo (imágenes, contenedores, volúmenes, redes). — **COMPLETADO**
- ⏭ Día 3 (16 oct): docker-compose + nginx + php-fpm. Hola mundo servido por el stack.

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

## 9. Calibración del usuario

- **Tiempo:** tardes y noches, 2-5 horas/día.
- **Días:** todos.
- **Nivel:** React básico, PHP promedio, Firebase/Git/Docker muy reducido.
- **Preferencia:** profundizar y aprender de verdad (ritmo pausado, foco en el porqué).

## 10. Convenciones

- Idioma de trabajo: español.
- Comentarios de código y nombres: claros; seguir estándares PSR en PHP.

---

## 11. Tareas de cierre del Día 2 (pendientes)

Sin incidencias técnicas. Solo cierre administrativo:

- [ ] Abrir el PR de `feature/docker-lab` -> `main` con `closes #N`
      (título sugerido: "Laboratorio de fundamentos de Docker").
- [ ] Reproducir el shadowing con volumen no vacío **y** con bind mount.
- [ ] Limpieza total del laboratorio:
      `docker rm -f`, `docker rmi`, `docker volume rm`, `docker network rm`.
- [ ] Escribir `docs/docker-notas.md` (contenido detallado en §13).

**Estado del repositorio:** `main` limpio y sincronizado con `origin/main`.
La rama `feature/docker-lab` contiene el commit del laboratorio y está subida.

---

## 12. Bitácora — Día 1 (14 oct 2026) — COMPLETADO

### Hecho
- `git init`, creación de toda la estructura de carpetas del monorepo con
  `.gitkeep`, `.gitignore` y `.env.example`.
- README inicial.
- Primeros commits con mensajes convencionales.
- `git remote add origin` + `git push -u origin main`.
- Activación de **protección de rama `main`** en GitHub (sin push directo).
- Issue #1 "Documentar contexto y bitácora del proyecto".
- Rama `feature/docs-contexto`, commit, push, PR con `closes #1`.
- **Squash and merge** del PR; borrado de la rama local tras el merge.

### Aprendido
- Flujo real de rama -> PR -> revisión -> merge.
- La rama protegida obliga a trabajar por PR (no es un estorbo, es el hábito).
- Squash and merge deja la historia de `main` legible.

---

## 13. Bitácora — Día 2 (15 oct 2026) — COMPLETADO

### Conceptos cubiertos
- Contenedor vs máquina virtual: namespaces (aislamiento) + cgroups (límites) +
  filesystem por capas. Un contenedor es un proceso, no un SO.
- Imagen (plantilla inmutable por capas + metadatos) vs contenedor (proceso +
  namespaces + cgroups + capa de escritura desechable creada al crear el
  contenedor y destruida con `docker rm`).
- Registro de imágenes, `pull`/`push`, reutilización de capas por hash.
- Volúmenes (persistencia fuera del contenedor) y redes (aislamiento + DNS
  interno por nombre de contenedor).

### Práctica realizada
- `docker --version`, `docker info`, `docker run hello-world`, `docker ps` vs `ps -a`.
- `docker pull nginx:alpine`, `docker images`, `docker history`, `docker inspect`.
- `docker/lab/` con `Dockerfile` (FROM/WORKDIR/COPY/EXPOSE/CMD), `index.html` y
  `.dockerignore`; `docker build -t cancionero-lab:0.1 .`
- `docker run -d --name lab -p 8080:80 ...` -> página servida en `localhost:8080`.
- `docker logs`, `docker exec -it lab sh`, `docker stats`, `stop`, `start`, `restart`.
- Demostración de efimeridad: archivo en `/tmp` perdido tras `docker rm` + `run`.
- Volumen con nombre + prueba de persistencia a través de `docker rm`.
- Red propia `cancionero-lab-net` + DNS por nombre entre contenedores.
- Contraste en la red `bridge` por defecto.

### INCIDENCIA DE GIT — resuelta
Se hizo el commit del laboratorio **estando en `main`**, antes de crear la rama
`feature/docker-lab`. Diagnóstico: `git log origin/main..main --oneline` sin
salida, es decir, `main` local idéntico al remoto (el commit ya estaba subido).

Resolución aplicada:
```bash
git switch -c feature/docker-lab    # la rama apunta al commit, el trabajo se salva
git branch -f main origin/main      # main vuelve atrás sin tocar el working tree
git push -u origin feature/docker-lab
```

**Aprendido:** el commit nunca estuvo en riesgo mientras existiera una referencia
a él. `git branch -f` mueve una rama sin tocar archivos, a diferencia de
`git reset --hard`. Y `git reflog` es la red de seguridad definitiva.
De aquí salió la regla de oro de §6.

### CORRECCIONES IMPORTANTES (el asistente se equivocó y se corrigió en sesión)

**1. El "403 por shadowing" no apareció.**
El asistente predijo un 403 al montar un volumen vacío sobre
`/usr/share/nginx/html`. No ocurrió porque **Docker copia el contenido de la
imagen dentro de un volumen con nombre que esté vacío** (auto-populate).
Reglas reales:

| Montaje | ¿Copia el contenido de la imagen? | Efecto |
|---|---|---|
| Volumen con nombre **vacío** | Sí, una sola vez | El servicio arranca con los archivos de la imagen |
| Volumen con nombre **con datos** | No | Tapa por completo el contenido de la imagen |
| Bind mount a carpeta del host | Nunca | Solo existe lo que hay en la carpeta del host |

**Gotcha crítico:** la copia ocurre UNA vez y **nunca se actualiza**. Reconstruir
la imagen con un `index.html` nuevo y reutilizar el volumen sigue sirviendo el
HTML viejo. Existe la opción `volume-nocopy` con sintaxis `--mount`.

**2. Elección de imagen en contenedores de un solo uso (helper).**
`docker run --rm -v vol:/data alpine sh -c '...'` es correcto: el contenedor
solo ejecuta un comando, no necesita nginx. Lo que determina la copia automática
**no es el nombre de la imagen** sino la combinación (ruta destino × contenido de
la imagen en esa ruta × si el volumen está vacío).

**3. Ping por IP falló desde la red `bridge`.**
El asistente armó mal el experimento: `web-lab` estaba en `cancionero-lab-net`
y el cliente en `bridge`. Docker instala `DOCKER-ISOLATION-STAGE-1` y `-2` en
iptables y **descarta el tráfico entre redes distintas**. Cada red es una
frontera de seguridad. Corrección aplicada: probar en la misma red.

Regla grabada: en la red `bridge` por defecto los contenedores se alcanzan **por
IP pero no por nombre** (no hay DNS). En redes definidas por el usuario funciona
el nombre. La IP solo tiene sentido dentro de su propia red. `docker network
connect` permite añadir un contenedor a una segunda red sin recrearlo — así se
conecta un reverse proxy a la red pública y a la privada a la vez.

### Entregables verificados
- [x] `localhost:8080` sirvió la página desde imagen propia.
- [x] Contenedor con volumen funcionando en `8081`.
- [x] `docker exec` dentro del contenedor.
- [x] DNS por nombre funcionando en `cancionero-lab-net`.
- [x] Autoevaluación del Día 2 respondida y corregida.
- [ ] Reproducir el shadowing con volumen no vacío y con bind mount.
- [ ] Limpieza total del laboratorio.
- [ ] PR del Día 2 abierto y mergeado.
- [ ] `docs/docker-notas.md` escrito con las palabras del usuario.

### Notas pendientes de escribir en `docs/docker-notas.md`
- Tabla de comportamiento de montajes (arriba) con el porqué de cada fila.
- Regla del helper container: la imagen mínima que baste; la copia depende de la
  ruta destino, no de la imagen.
- Regla de red: la red es la unidad de aislamiento; la IP solo existe dentro de
  su red; el DNS por nombre solo existe en redes definidas por el usuario.
- Gotcha del volumen: la copia automática ocurre una vez y nunca se actualiza.

---

## 14. Próxima sesión — Día 3 (16 oct 2026)

**Meta:** levantar el stack real (nginx + php-fpm + frontend) con
`docker-compose`, y servir un "hola mundo" a través de nginx haciendo proxy a
php-fpm por FastCGI.

**Contenido previsto:**
1. Verificar que el PR del Día 2 está mergeado y la rama borrada.
2. Concepto: por qué `docker-compose` (declarativo, reproducible, un comando) y
   qué resuelve frente a `docker run` a mano.
3. `docker-compose.yml` con servicios `nginx`, `php`, `frontend`, red propia y
   volúmenes (bind mount para código, volumen para config).
4. `nginx/default.conf` con `root` para estáticos y `fastcgi_pass php:9000`.
5. `backend/public/index.php` como front controller mínimo.
6. `docker-compose.override.yml` para el modo desarrollo.
7. Verificación: `http://localhost:8080` sirviendo el frontend y
   `http://localhost:8080/api/hola` respondiendo desde PHP.
8. PR con `closes #N`.

**Prerrequisitos:** Docker Desktop arrancado; puertos 8080 y 8081 libres.

**Recordatorio para el asistente:** el usuario tiene nivel "muy reducido" en
Docker; explicar cada directiva del compose y del `default.conf` antes de
usarla. No introducir `docker-compose` antes de que el Día 2 esté cerrado.

---

## 15. Historial del documento

- **2026-10-15** — Versión inicial (Día 1): contexto, decisiones, plan,
  convenciones y bitácora del Día 1.
- **2026-10-15** — Actualización tras el Día 2: bitácora de Docker, tres
  correcciones técnicas, incidencia de Git resuelta, autoevaluación cerrada,
  reestructuración de pendientes.
