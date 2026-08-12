## Exploration: Restructura del sitio público (web-restructure)

Fuente de verdad de requisitos: `SDD.md` (header, Nosotros, Restaurantes, Eventos, Servicios, Home, Footer).

### Current State

Stack: Laravel 11 monolito + Blade/Alpine/Swiper + Filament v3. Rutas públicas en `routes/web.php`. Change paralelo `refugio-platform` sigue en `apply` (fase 7 SEO pendiente); sus specs frontend describen el clon WP actual y **chocan** con esta restructura.

**Header** (`resources/views/components/header.blade.php`): nav = Restaurantes / Eventos / Visítanos (`about`). CTA derecha «¡Reserva aquí!» (WhatsApp) ya existe. Menú overlay reusa los mismos `$navItems`. Sticky tiene botón extra «¿Qué te provoca?».

**Nosotros / Visítanos** (`/nosotros` → `AboutController` + `pages/about.blade.php`): hero «Conoce más sobre nosotros»; sección mapa + card «Ubícanos». No hay bloque «¡Hola! Somos Refugio…» ni copy SDD. `VisitInfo` singleton ya tiene address/schedule/map/amenities/`about_content` (este último poco usado en la vista).

**Restaurantes** (`/restaurantes`): hero título «Restaurantes»; filtros categoría + grilla (`RestaurantCard`) — **mantener**. Sin componente «¿Nos visitas?».

**Eventos** (`/eventos`): hero «Eventos»; grilla de `Event` por fecha (`event-card`); newsletter + banner «Organiza un evento». SDD pide **4 cards estáticas de tipos de experiencia**, no listado por fechas.

**Servicios**: **no existe** ruta, modelo ni página.

**Home** (`HomeController` + `pages/home.blade.php`):
1. `hero-slider` — Swiper con 2 slides hardcodeados (imagen CMS slide1 + slide ubicación); prev/next siempre visibles; **sin video**.
2. `hero-slogan` — «Juntos todo sabe mejor» + 3 hotspots (no el copy Hola SDD).
3. `restaurant-grid` — collage + logos vía `HomeRestaurantFeature` (configurable en Filament «Grid home»).
4. `event-carousel` + `instagram-feed` + blog opcional (`show_blog_section`).
5. Sin preview de servicios, sin «¿Nos visitas?», sin bloque contacto 4 columnas.

**Footer**: col Visítanos (address/horarios) + Nosotros/Convocatorias/Visítanos + legal/social. No alinea con las 3 columnas SDD.

**CMS relevante**:
- `HeroSlide` / `HeroSlideResource` — solo imagen `background_image`; sin media video ni `media_type`.
- `HomeRestaurantFeature` — orden/activo de restaurantes en home (reusable para carousel logos).
- `CtaBlock` — 3 tipos (`evento`/`renta`/`contacto`) usados en contacto, no los 4 bloques SDD.
- `VisitInfo` / `ManageVisitInfo` — base para «¿Nos visitas?».
- Mirrored pages: términos, privacidad, libro reclamaciones, convocatoria, contacto. **Faltan** FAQ, Reglamento Pet Friendly, Política estacionamiento, Descuentos U. Lima (enlaces footer col2).
- Blog routes siguen vivas; home ya puede ocultar sección con toggle.

### Affected Areas

- `resources/views/components/header.blade.php` — nav → Nosotros / Restaurantes / Eventos; sync overlay/sticky.
- `resources/views/components/footer.blade.php` — 3 columnas SDD (nav, secundarios, legal+social).
- `resources/views/pages/home.blade.php` + `HomeController.php` — composición nueva; quitar blog/events/IG según SDD (o dejar IG fuera de scope si no se menciona — SDD no lista IG; tratar como remoción o defer).
- `resources/views/components/hero-slider.blade.php` + `app/Models/HeroSlide.php` + `HeroSlideResource` — video(s); botones circulares solo si count > 1.
- `resources/views/components/hero-slogan.blade.php` (o nuevo) — sección Hola + texto SDD.
- Nuevo componente `x-visit-us` / similar — reutilizar en Nosotros, Restaurantes, Eventos, Servicios, Home.
- `resources/views/pages/about.blade.php` — hero «¿Quienes Somos?», Hola split, visit-us.
- `resources/views/pages/restaurants/index.blade.php` — hero + visit-us.
- `resources/views/pages/events/index.blade.php` (+ posible `EventController`) — reemplazar listado por fechas por 4 cards de oferta; detalle `/eventos/{slug}` queda huérfano o secondary.
- Nueva ruta `/servicios` + vista + posible model/seeder `Service` (icon+label+sort).
- Nuevo bloque home «Conoce nuestros servicios» + CTA Ver más → `/servicios`.
- Nuevo bloque home «¿Dudas? ¡Contáctanos!» (4 contact cards) — extender `CtaBlock` o seeder/config.
- Footer links secundarios — nuevas mirrored pages o páginas estáticas Blade.
- `openspec/changes/refugio-platform/specs/frontend/spec.md` — conflictos: nav, events listing by date, hero image carousel, blog en nav.

### Approaches

1. **Restructura modular Blade + CMS puntual** — Componentes reutilizables (`visit-us`, `hola-section`, `services-grid`, `contact-blocks`, `logo-carousel`); extender `HeroSlide` con colección video; seed/CMS para Service y EventOffer cards; reusar `HomeRestaurantFeature` para logos; footer/header data arrays centralizados.
   - Pros: alinea con arquitectura actual; bajo acoplamiento; Filament ya familiar; rollback por vistas.
   - Cons: hay que decidir destino de Event CMS actual y blog; overlap con `refugio-platform` apply.
   - Effort: Medium

2. **Páginas estáticas hardcodeadas (mínimo CMS)** — Textos/icons/cards en Blade/config PHP; solo video hero y logos vía Filament.
   - Pros: entrega rápida de IA visual; menos migraciones.
   - Cons: marketing no edita servicios/eventos/contacto sin deploy; peor para producto a largo plazo.
   - Effort: Low–Medium

3. **Page-builder / bloques CMS genéricos** — Modelo `PageSection` flexible por página.
   - Pros: máxima flexibilidad editorial.
   - Cons: over-engineering vs SDD concreto; alto esfuerzo; fuera del patrón del repo.
   - Effort: High

### Recommendation

**Approach 1**: restructura modular Blade + extensiones CMS mínimas.

- Extraer **`x-visit-us`** desde VisitInfo (copy SDD + mapa de fondo) y montarlo en las 5 superficies.
- Header/footer: un solo source de nav items (array o View Composer) para no diverger.
- Home: hero video-capable; Hola; logo carousel (HomeRestaurantFeature); preview servicios; visit-us; contact 4-up; **sin blog** (default `show_blog_section=false` o quitar del template).
- Eventos públicos: 4 offer cards (contenido seed/CMS `EventOffer` o config); mantener `Event` admin solo si se necesita después para shows puntuales — **fuera del listado principal**.
- Servicios: nueva entidad ligera `ServiceItem` (icon media/SVG key, title, sort, is_active) + Filament resource simple.
- Coordinar con `refugio-platform`: este change **supersede** los requirements frontend de nav/home/events; no archivar platform hasta cerrar SEO/infra compartida.

### Risks

- **Overlap `refugio-platform`**: specs/tasks aún «apply»; verificar/archive pueden marcar fallos si no se actualiza el delta o se declara supersede explícito.
- **Eventos por fecha → cards estáticas**: ruptura de UX/SEO de `/eventos/{slug}`; decidir redirect, página secundaria o deprecar.
- **Hero video**: peso, autoplay muted policies, fallback imagen, storage Spatie; botones laterales solo si >1 — requiere JS Swiper condicional.
- **Horarios SDD vs VisitInfo seed**: SDD usa «Hasta las 10 p.m. / 1:00 p.m. / 3:00 p.m.» (posible typo 1 a.m.); alinear copy con negocio.
- **Footer col2 pages** (FAQ, Pet Friendly, estacionamiento, Descuentos U. Lima): contenido aún no en repo — bloquearía closure visual si no hay copy/assets.
- **Instagram en home**: no está en SDD; remover sin confirmación puede sorprender stakeholders.
- **Blog**: rutas pueden quedar vivas vía footer col2 («blog») aunque home no lo muestre — consistente con SDD footer.

### Open questions (para proposal)

1. ¿Instagram feed se elimina del home o se conserva fuera de SDD?
2. ¿`/eventos/{slug}` y admin Event se mantienen, o se archivan?
3. ¿Servicios e Event offer cards 100% CMS o seed fijo v1?
4. ¿Páginas footer col2 son mirrored WP, Blade estáticas nuevas, o placeholders?
5. ¿Servicios entra al header? (SDD header solo lista Nosotros/Restaurantes/Eventos; Servicios alcanzable desde home «Ver más» / footer?).

### Ready for Proposal

**Yes** — hay suficiente código y requisitos SDD para proponer scope, supersede de specs frontend conflictivas, y plan de componentes/CMS. El orchestrator debe pedir confirmación de las open questions (especialmente Eventos slug e Instagram) antes o durante `sdd-propose`.
