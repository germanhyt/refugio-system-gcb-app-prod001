# Tasks: Estrategia GEO para Refugio Gastronómico

## Fase 1 — Fundaciones técnicas y datos estructurados (implementación inicial)

### 1.1 Servicio SeoService
- [ ] 1.1.1 Crear `app/Services/SeoService.php` con método `organizationJsonLd(SiteSetting, VisitInfo): string`
- [ ] 1.1.2 Método `restaurantJsonLd(Restaurant, SiteSetting): string` (FoodEstablishment + parentOrganization)
- [ ] 1.1.3 Método `breadcrumbJsonLd(array $trail): string`
- [ ] 1.1.4 Método `faqJsonLd(array $page): string` (FAQPage desde bloque type=faq)
- [ ] 1.1.5 Método `eventJsonLd(Event): string` (Event schema)

### 1.2 Inyección en layout
- [ ] 1.2.1 Añadir `@stack('json-ld')` al `<head>` de `layouts/app.blade.php`
- [ ] 1.2.2 Añadir `<link rel="canonical">` con `url()->current()`
- [ ] 1.2.3 Añadir Twitter Card tags (card, title, description, image) reusing OG values

### 1.3 Push JSON-LD desde controladores
- [ ] 1.3.1 `HomeController`: push JSON-LD parque
- [ ] 1.3.2 `RestaurantController::show`: push parque + concepto + BreadcrumbList
- [ ] 1.3.3 `StaticPageController::faq`: push FAQPage
- [ ] 1.3.4 `EventController::show`: push Event schema (si aplica)

### 1.4 Sitemap XML
- [ ] 1.4.1 Crear `app/Console/Commands/GenerateSitemapCommand.php` (`refugio:sitemap`)
- [ ] 1.4.2 Añadir ruta `GET /sitemap.xml` en `routes/web.php`
- [ ] 1.4.3 Añadir línea `Sitemap:` a `public/robots.txt`

### 1.5 llms.txt
- [ ] 1.5.1 Crear `public/llms.txt` curado (parque, conceptos, enlaces clave)

### 1.6 Tests
- [ ] 1.6.1 `tests/Feature/SeoTest.php`: home emite JSON-LD parque válido
- [ ] 1.6.2 Detalle restaurante emite FoodEstablishment + BreadcrumbList
- [ ] 1.6.3 FAQ emite FAQPage
- [ ] 1.6.4 Toda página tiene canonical + twitter cards
- [ ] 1.6.5 `/sitemap.xml` 200 + xml + entradas clave
- [ ] 1.6.6 `/llms.txt` 200
- [ ] 1.6.7 `php artisan test` verde; `npm run build` verde

## Fase 2 — Contenido citable y GBP (futuro, no se ejecuta ahora)
- 2.1 FAQ maestra answer-first + FAQ por concepto (top 5–8)
- 2.2 Campos dietéticos/ocasión en CMS (migración + Filament) → amenityFeature
- 2.3 Marcar páginas estáticas (pet-friendly, parqueo, ulima) con schema
- 2.4 Guías de barrio ("Dónde comer en Surco", "Food parks en Lima")
- 2.5 Gestión activa Google Business Profile (operativo, externo al código)

## Fase 3 — Autoridad distribuida y medición (futuro)
- 3.1 Outreach a publicaciones locales
- 3.2 Consistencia en directorios (Yelp, TripAdvisor, PedidosYa, Rappi)
- 3.3 Tracking GA4 de tráfico de IA + dashboard de citación
- 3.4 Re-test de 10 consultas objetivo vs. línea base
