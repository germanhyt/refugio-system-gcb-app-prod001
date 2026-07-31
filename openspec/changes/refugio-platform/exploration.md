# Exploration: Clon Refugio Gastronómico

## Current State

### Sitio origen
- **URL**: https://refugiogastronomico.pe/
- **Stack actual**: WordPress 6.1 + Tema Rey + Elementor + Jet Engine + Yoast SEO
- **Slogan**: "Juntos todo sabe mejor"
- **Ubicación**: Av. Javier Prado Este 4492, Santiago de Surco, Lima
- **Contenedor máximo**: 1440px (`data-container="1440"`)

### Inventario de páginas (sitemap)

| Ruta | Tipo | Prioridad |
|------|------|-----------|
| `/` | Home | P0 |
| `/restaurantes/` | Listado + filtros | P0 |
| `/restaurantes/{slug}/` | Detalle restaurante (22 locales) | P0 |
| `/eventos/` | Listado eventos | P0 |
| `/eventos/{slug}/` | Detalle evento (8 eventos) | P0 |
| `/contacto/` | Contacto + FAQs + ubicación | P0 |
| `/nosotros/` | Sobre nosotros + mapa | P1 |
| `/blog/` | Blog foodies | P1 |
| `/convocatorias/` | Convocatorias laborales | P2 |
| `/libro-de-reclamaciones/` | Libro de reclamaciones | P2 |
| `/politica-privacidad/` | Legal | P2 |
| `/terminos-y-condiciones/` | Legal | P2 |

### Restaurantes (22 locales)

Nashmy's, Barrio Wok, Caldos Doris, Ramen Ya!, Ahumare, Cavenecia, Don Melchor, Tortas Gaby, Mr Smash, La Victoria, Hanzo Express, Refugio Bar, SISA Coffee & Wine, La 22 Sanguchería, La Choza de la Anaconda, Limanesas, Cremoladas Curich, Broaster BROS, Barrio Mancora, Saltao Wok Food, Caja China Criolla, Anticuchos Anticuching.

### Categorías de restaurantes (filtros)

- Todos, Bar, Cafés y postres, Internacional, Peruana, Rápida
- Taxonomía WP adicional: `etiquetas-restaurant` (cafetería, comida saludable, desayunos)

---

## Auditoría de diseño (UI/UX)

### Identidad visual

| Token | Valor | Uso |
|-------|-------|-----|
| Fondo header | Gradiente negro `#00000085` | Header sticky/absolute |
| Logo | SVG `logo-v2.svg` | Header + footer |
| Contenedor | max-width 1440px | Layout principal |
| Tipografía | `--primary-ff` (tema Rey) | Headings y body |
| CTA principal | "¡RESERVA AQUÍ!" → WhatsApp | Header fijo |
| Botón scroll | "TOP" | Flotante inferior derecha |

### Componentes Home (`/`)

1. **Hero Slider** — Carrusel fullscreen con 3+ slides:
   - Título: "Juntos todo **sabe** mejor" (palabra destacada en bold/color)
   - Subtítulos rotativos: "Disfruta de espacios impresionantes", "La mejor propuesta gastronómica", "Escapa de la rutina"
   - Imagen de fondo full-bleed por slide
   - Texto descriptivo Lorem/institucional

2. **Sección Restaurantes** — Grid de logos/cards:
   - Título: "¡Conoce nuestros **restaurantes**!"
   - Cards con logo del restaurante + hover
   - Link a `/restaurantes/{slug}/`

3. **Sección Eventos** — Timeline/cards horizontales:
   - Título: "Eventos & actividades"
   - Card: día abreviado (Jue/Sáb/Vie) + número + nombre artista/banda
   - Link a detalle evento

4. **Blog Foodies** — Grid de posts:
   - Título: "Blog de foodies para foodies"
   - Card: imagen, autor (@handle), categoría, título, rating estrellas

5. **Instagram Feed** — Embed Smash Balloon:
   - Título: "Síguenos y **únete al club**"
   - Grid 3 columnas de posts IG
   - Botón "Load More"

6. **CTA Triple** — 3 bloques al pie:
   - "¿Quieres liderar **un evento**?" → Más info
   - "¿Quieres rentar **un local**?" → Más info
   - "¿Quieres **contactarnos**?" → Más info

### Componentes `/restaurantes/`

1. **Hero** — Título "Restaurantes" duplicado (diseño Elementor)
2. **Cómo ordenar** — 3 pasos con iconos:
   - SELECCIONA un restaurante & revisa la carta
   - PIDE los platos que desees
   - ESPERA por tu comida
3. **Filtros por categoría** — Pills/tabs: Todos, Bar, Cafés y postres, Internacional, Peruana, Rápida
4. **Grid de restaurantes** — Cards con imagen + nombre, filtrable por categoría
5. **Paginación** — 2 páginas de resultados

### Componentes detalle restaurante (`/restaurantes/{slug}/`)

- Header compartido con botón "Regresar" → listado
- Hero con nombre del restaurante (H1)
- Imagen destacada / galería
- Mapa Google Maps embebido
- CTA "¿Se te abrió el apetito?" + botón "Pide ahora"
- Menú off-canvas (popup Elementor) con navegación secundaria

### Componentes `/eventos/`

- Listado de eventos (mismo formato que home)
- Formulario newsletter: Nombre + Correo + "Suscríbete"
- CTA: "¡Organiza un evento con nosotros!"

### Componentes Visítanos (fusión `/contacto/` + `/nosotros/`)

> Nota: `/visitanos/` no existe en el sitio actual. Se modela como sección CMS editable.

| Campo | Contenido actual |
|-------|------------------|
| Dirección | Av. Javier Prado Este 4492 – Santiago de Surco |
| Horarios | Dom–Mié 7am–10pm, Jue 7am–12am, Vie–Sáb 7am–1am |
| Teléfono | 994 848 723 / Reservas 991 318 720 |
| Email | hola@refugiogastronomico.pe |
| Mapa | Google Maps embed |
| Accesos | Peatonal: Av. Manuel Olguín / Vehicular: Av. Javier Prado Este 4492 |
| Amenidades | Pet Friendly, 3h estacionamiento gratis con S/50 consumo |

### Header global

- Logo centrado
- Nav izquierda (desktop): Restaurantes, Eventos
- Nav derecha (desktop): Nosotros, Blog, Contacto
- CTA WhatsApp: "¡RESERVA AQUÍ!"
- Menú hamburguesa (mobile/tablet) → off-canvas panel
- Redes: Instagram, Facebook, TikTok

### Footer

- Links legales: Política privacidad, Términos, Libro reclamaciones
- Copyright El Refugio

---

## Módulos administrables (Filament)

| Módulo | Entidad CMS | Campos clave |
|--------|-------------|--------------|
| Banners/Hero | `HeroSlide` | título, subtítulo, imagen, CTA, orden, activo |
| Grid restaurantes home | `HomeRestaurantFeature` | restaurante_id, orden, destacado |
| Restaurantes | `Restaurant` | nombre, slug, logo, imagen, categorías, descripción, menú PDF, WhatsApp, mapa, activo |
| Categorías | `RestaurantCategory` | nombre, slug, orden |
| Eventos | `Event` | título, slug, fecha, hora, imagen, descripción, artista, activo |
| Visítanos | `VisitInfo` (singleton) | dirección, horarios JSON, teléfonos, email, mapa embed, accesos, amenidades |
| Blog | `Post` | título, slug, autor, categoría, imagen, rating, contenido |
| CTAs footer | `CtaBlock` | título, subtítulo, link, tipo (evento/renta/contacto) |
| Configuración | `SiteSetting` | logo, redes sociales, WhatsApp, SEO meta |
| Newsletter | `NewsletterSubscriber` | nombre, email, fecha |

---

## Affected Areas

| Área | Impacto |
|------|---------|
| `app/Models/*` | Nuevo — todas las entidades CMS |
| `app/Filament/Resources/*` | Nuevo — CRUD admin |
| `app/Console/Commands/Scrape*` | Nuevo — importación desde WP |
| `resources/views/*` | Nuevo — frontend Blade |
| `database/migrations/*` | Nuevo — esquema completo |
| `public/assets/*` | Nuevo — imágenes scrapeadas |

---

## Approaches

### 1. Scraper one-shot + CMS manual
- **Pros**: Rápido, datos iniciales automáticos
- **Cons**: Contenido puede quedar desactualizado
- **Effort**: Medium

### 2. Scraper + sync periódico (scheduled)
- **Pros**: Mantiene paridad con sitio origen durante migración
- **Cons**: Complejidad adicional, dependencia del WP
- **Effort**: High

### 3. Solo CMS manual (sin scraper)
- **Pros**: Datos limpios desde cero
- **Cons**: Lento, 22 restaurantes + imágenes a cargar manualmente
- **Effort**: High (operativo)

---

## Recommendation

**Approach 1 + extensión a 2**: Scraper inicial completo (HTML, imágenes, metadatos) como comando Artisan `php artisan refugio:import`. Post-migración, todo se gestiona desde Filament. Sync periódico opcional como fase 2.

**Stack**: Laravel 11 + Filament v3 + Livewire 3 + Tailwind + Spatie Media Library + Spatie Sluggable + Spatie SEO.

**Frontend**: Blade components modulares replicando secciones Elementor. Animaciones con Alpine.js. Swiper.js para carruseles.

---

## Risks

| Riesgo | Mitigación |
|--------|------------|
| Derechos de autor en imágenes | Scraper solo para migración interna; reemplazar assets con autorización |
| Instagram feed no replicable sin API | Usar embed oficial o módulo manual de "posts destacados" |
| Fidelidad pixel-perfect | Design tokens documentados; revisión visual por breakpoint |
| Menús PDF por restaurante | Scraper descarga PDFs; admin permite reemplazo |

---

## Ready for Proposal

**Sí.** Inventario completo, design audit documentado, entidades CMS definidas. Proceder con proposal → specs → design → tasks.
