# Tasks: Plataforma Refugio Gastronómico

## Phase 1: Foundation

- [x] 1.1 Crear proyecto Laravel 11: `composer create-project laravel/laravel .`
- [x] 1.2 Instalar Filament v3: `composer require filament/filament` + `php artisan filament:install --panels`
- [x] 1.3 Instalar dependencias: spatie/laravel-medialibrary, spatie/laravel-sluggable, symfony/dom-crawler, symfony/css-selector
- [x] 1.4 Configurar Tailwind CSS 3 + Alpine.js + Swiper.js via Vite
- [x] 1.5 Crear `resources/css/tokens.css` con design tokens del audit
- [x] 1.6 Configurar `.env` (DB, APP_URL, FILESYSTEM_DISK=public)
- [x] 1.7 Crear usuario admin seed: `php artisan make:filament-user`

## Phase 2: Database & Models

- [x] 2.1 Migration `create_restaurant_categories_table`
- [x] 2.2 Migration `create_restaurants_table` + pivot `restaurant_category`
- [x] 2.3 Migration `create_events_table`
- [x] 2.4 Migration `create_hero_slides_table`
- [x] 2.5 Migration `create_visit_info_table` (singleton)
- [x] 2.6 Migration `create_home_restaurant_features_table`
- [x] 2.7 Migration `create_cta_blocks_table`
- [x] 2.8 Migration `create_site_settings_table` (singleton)
- [x] 2.9 Migration `create_newsletter_subscribers_table`
- [x] 2.10 Crear Eloquent models con relaciones, slugs, media collections, casts
- [x] 2.11 Seeders: RestaurantCategorySeeder (5 categorías), CtaBlockSeeder (3 CTAs) + VisitInfo/SiteSetting

## Phase 3: Scraper

- [x] 3.1 Crear `app/Services/Scraper/SitemapParser.php` — parse sitemaps XML
- [x] 3.2 Crear `app/Services/Scraper/ImageDownloader.php` — download + checksum dedup
- [x] 3.3 Crear `app/Services/Scraper/RestaurantScraper.php` — scrape + upsert restaurants
- [x] 3.4 Crear `app/Services/Scraper/EventScraper.php` — scrape + upsert events
- [x] 3.5 Crear `app/Services/Scraper/PageScraper.php` — import VisitInfo + SiteSetting
- [x] 3.6 Crear `app/Services/Scraper/HeroScraper.php` — import homepage slides
- [x] 3.7 Crear `app/Console/Commands/RefugioImportCommand.php` con flags: `--all`, `--discover`, `--force`
- [x] 3.8 Ejecutar `php artisan refugio:import --all` y verificar 22 restaurantes + 8 eventos

## Phase 4: Filament Admin

- [x] 4.1 `HeroSlideResource` — CRUD con reorder (sort_order), image upload, toggle activo
- [x] 4.2 `RestaurantResource` — CRUD con categorías (Select multiple), logo, imagen, menú PDF
- [x] 4.3 `RestaurantCategoryResource` — CRUD con sort_order
- [x] 4.4 `EventResource` — CRUD con date picker, image upload
- [x] 4.5 `HomeRestaurantFeatureResource` — RelationManager en Restaurant o resource dedicado
- [x] 4.6 `CtaBlockResource` — CRUD con enum type (evento/renta/contacto)
- [x] 4.7 `VisitInfo` Filament Page — singleton edit form (address, schedule repeater, map embed)
- [x] 4.8 `SiteSettings` Filament Page — singleton (logo, redes, WhatsApp, SEO)
- [x] 4.9 `NewsletterSubscriberResource` — listado read-only con export CSV
- [x] 4.10 Configurar Filament navigation groups: Contenido, Configuración, Marketing

## Phase 5: Frontend Layout

- [x] 5.1 Crear `resources/views/layouts/app.blade.php` — HTML shell, meta SEO, Vite assets
- [x] 5.2 Crear `components/header.blade.php` — nav desktop/mobile, logo, WhatsApp CTA, off-canvas
- [x] 5.3 Crear `components/footer.blade.php` — links legales, redes sociales, copyright
- [x] 5.4 Crear `components/scroll-top.blade.php` — botón "TOP" flotante
- [x] 5.5 Implementar responsive menu con Alpine.js (hamburger → off-canvas panel)

## Phase 6: Frontend Pages

- [x] 6.1 `HomeController` + `pages/home.blade.php` — ensamblar todas las secciones
- [x] 6.2 `components/hero-slider.blade.php` — Swiper fullscreen con slides del CMS
- [x] 6.3 `components/restaurant-grid.blade.php` — grid logos home (HomeRestaurantFeature)
- [x] 6.4 `components/event-carousel.blade.php` — cards eventos con día/fecha/título
- [x] 6.5 `components/cta-triple.blade.php` — 3 bloques CTA del footer home
- [x] 6.6 `RestaurantController@index` + `restaurants/index.blade.php` — filtros + grid + paginación
- [x] 6.7 `components/category-filter.blade.php` — pills con Alpine.js filter (o Livewire)
- [x] 6.8 `components/restaurant-card.blade.php` — card con imagen, nombre, hover effect
- [x] 6.9 `RestaurantController@show` + `restaurants/show.blade.php` — detalle con mapa, CTA
- [x] 6.10 `EventController@index` + `events/index.blade.php` — listado + newsletter form
- [x] 6.11 `EventController@show` + `events/show.blade.php` — detalle evento
- [x] 6.12 `ContactController` + `pages/contact.blade.php` — FAQs, ubicación, mapa, CTAs
- [x] 6.13 `AboutController` + `pages/about.blade.php` — nosotros + mapa
- [x] 6.14 `NewsletterController@store` — validación + guardar suscriptor

## Phase 7: SEO & Performance

- [ ] 7.1 Crear `app/Services/SeoService.php` — meta tags dinámicos por página
- [ ] 7.2 Generar sitemap XML: `php artisan make:command GenerateSitemap`
- [ ] 7.3 Schema.org JSON-LD en layout (Organization, Restaurant, Event)
- [ ] 7.4 Configurar Spatie Media conversions WebP para todas las imágenes
- [ ] 7.5 Lazy loading en imágenes + preload hero first slide
- [ ] 7.6 Configurar route caching y view caching para producción

## Phase 8: Testing & QA

- [ ] 8.1 Feature test: `GET /` returns 200 with hero section
- [ ] 8.2 Feature test: `GET /restaurantes/ahumare` returns 200 for active restaurant
- [ ] 8.3 Feature test: `GET /restaurantes/inactive-slug` returns 404
- [ ] 8.4 Feature test: category filter returns only matching restaurants
- [ ] 8.5 Feature test: newsletter form creates subscriber
- [ ] 8.6 Unit test: SitemapParser discovers 22+ restaurant URLs
- [ ] 8.7 Unit test: RestaurantScraper upsert is idempotent
- [ ] 8.8 Visual QA checklist: comparar home, restaurantes, eventos, contacto vs origen en 375/768/1440px
- [ ] 8.9 Verificar admin: editar banner → refrescar home → cambio visible
- [ ] 8.10 Verificar admin: editar restaurante → refrescar detalle → cambio visible

## Phase 9: Deployment Prep

- [ ] 9.1 Crear `Dockerfile` + `docker-compose.yml` (PHP-FPM, MySQL, Nginx)
- [ ] 9.2 Documentar setup en `README.md` (install, migrate, seed, import, build)
- [ ] 9.3 Configurar `.env.example` con todas las variables necesarias
- [ ] 9.4 Script deploy: `migrate --force`, `storage:link`, `config:cache`, `route:cache`
