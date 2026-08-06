# Refugio Gastronómico

Plataforma web Laravel 11 + Filament 3 para [refugiogastronomico.pe](https://refugiogastronomico.pe).

## Requisitos

| Modo | Requisitos |
|------|------------|
| Local | PHP 8.2+, Composer, Node 20+, MySQL 8 |
| Docker | Docker + Docker Compose |

---

## 1. Desarrollo local

### 1.1 Clonar e instalar dependencias

```bash
git clone <repo-url> softapp-refugiogastronomico-complete-prod001
cd softapp-refugiogastronomico-complete-prod001

composer install
npm install
```

### 1.2 Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Ajusta en `.env` al menos:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=refugio_gastronomico
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos en MySQL:

```sql
CREATE DATABASE refugio_gastronomico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.3 Migrar, seed y storage


```bash
php artisan migrate --seed
php artisan storage:link
```

Usuario admin por defecto (seeder):

- Email: `admin@refugio.pe`
- Password: `RefugioAdmin2026!`

### 1.4 (Opcional) Importar contenido del sitio fuente

```bash
php artisan refugio:import --all
```

### 1.5 Levantar el entorno

**Opción A — todo junto (servidor + queue + logs + Vite):**

```bash
composer run dev
```

**Opción B — procesos separados:**

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev

# Terminal 3 (si usas colas)
php artisan queue:listen --tries=1
```

### 1.6 URLs

| Recurso | URL |
|---------|-----|
| Sitio | http://localhost:8000 |
| Admin Filament | http://localhost:8000/admin |

---

## 2. Docker (stack completo)

Incluye: PHP-FPM (`app`), Nginx (`web`), Caddy (`proxy`), MySQL (`db`), Redis (`redis`) y worker de colas (`queue`).

### 2.1 Configurar `.env`

```bash
cp .env.docker.example .env
```

Completa al menos:

```env
APP_KEY=                 # generar abajo
APP_URL=https://tu-dominio
APP_DOMAIN=tu-dominio
LETSENCRYPT_EMAIL=tu@email.com
DB_DATABASE=refugio_gastronomico
DB_PASSWORD=supersecreto
DB_ROOT_PASSWORD=supersecreto
```

Genera la key (en local, con PHP instalado):

```bash
php artisan key:generate --show
# Copia el valor en APP_KEY del .env
```

O dentro del contenedor después del primer build (ver 2.3).

### 2.2 Build y arranque

```bash
docker compose build
docker compose up -d
```

### 2.3 Post-arranque (primera vez)

```bash
docker compose exec app php artisan key:generate --force   # si APP_KEY está vacío
docker compose exec app php artisan migrate --force --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

Import opcional:

```bash
docker compose exec app php artisan refugio:import --all
```

### 2.4 Comandos útiles

```bash
docker compose ps
docker compose logs -f app
docker compose logs -f web
docker compose down
docker compose down -v   # también borra volúmenes (DB, storage, etc.)
```

Caddy escucha en los puertos **80** y **443** y usa `APP_DOMAIN` + `LETSENCRYPT_EMAIL` del entorno del host (variables del `.env` / shell) para TLS.

---

## 3. Docker en VPS

Usa `docker-compose.vps.yml` (sin Caddy propio; Nginx se une a la red externa `app_shared_network` para el proxy del servidor).

```bash
# Crear la red compartida una vez (si no existe)
docker network create app_shared_network

cp .env.docker.example .env
# Editar APP_KEY, DB_*, APP_URL, etc.

docker compose -f docker-compose.vps.yml build
docker compose -f docker-compose.vps.yml up -d

docker compose -f docker-compose.vps.yml exec app php artisan migrate --force --seed
docker compose -f docker-compose.vps.yml exec app php artisan storage:link
docker compose -f docker-compose.vps.yml exec app php artisan config:cache
docker compose -f docker-compose.vps.yml exec app php artisan route:cache
docker compose -f docker-compose.vps.yml exec app php artisan view:cache
```

---

## 4. Build de assets (producción / sin Vite)

```bash
npm ci
npm run build
```

En Docker, el build de Vite ya ocurre dentro de los Dockerfiles de `app` y `web`.

---

## 5. Tests

```bash
php artisan test
```

---

## Estructura Docker

```
docker/
  php/Dockerfile          # PHP 8.3-FPM + Composer + assets
  nginx/Dockerfile        # Nginx + assets (compose local)
  nginx/Dockerfile.vps    # Nginx para VPS
  nginx/default.conf
  nginx/default.vps.conf
  caddy/Caddyfile         # TLS + reverse proxy → web
```

| Compose | Uso |
|---------|-----|
| `docker-compose.yml` | Local / stack con Caddy |
| `docker-compose.vps.yml` | VPS con red `app_shared_network` |
