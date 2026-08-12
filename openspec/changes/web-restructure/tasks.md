# Tasks: Public site restructure (web-restructure)

> Design open Q (schedule): use SDD copy as written (incl. «p.m.») unless VisitInfo already has better copy; prefer updating VisitInfo seed/data to SDD text.

## Phase 1: Infrastructure

- [x] 1.1 Create `config/navigation.php` with `primary`, `footer_secondary`, `footer_legal` (no Servicios in primary)
- [x] 1.2 Migration: add `media_type` to `hero_slides`; register Spatie collection `background_video` on `HeroSlide`
- [x] 1.3 Migration + model `ServiceItem` (`app/Models/ServiceItem.php`): title, icon/media, description?, sort_order, show_on_home, is_active + scopes
- [x] 1.4 Migration + model `EventOffer`: title, slug?, summary?, cta_text/url?, sort_order, is_active + optional Spatie cover + scopes
- [x] 1.5 Migration + model `ContactBlock`: title, body, phones/emails?, sort_order, is_active + scopes

## Phase 2: Admin Filament

- [x] 2.1 Update `HeroSlideResource`: media_type image|video; conditional video upload; image as poster/fallback
- [x] 2.2 Create `ServiceItemResource` (Contenido, reorderable, active/show_on_home)
- [x] 2.3 Create `EventOfferResource` (Contenido, reorderable); leave Event resource decoupled from `/eventos`
- [x] 2.4 Create `ContactBlockResource` (Contenido, reorderable)

## Phase 3: Shared Blade components

- [x] 3.1 Create `x-hola-section` (`resources/views/components/hola-section.blade.php`)
- [x] 3.2 Create `x-visit-us` from `$visitInfo` (map + SDD schedule copy)
- [x] 3.3 Create `x-logo-carousel` from `HomeRestaurantFeature`
- [x] 3.4 Create `x-services-preview` (home subset + «Ver más» → servicios) and `x-services-grid` (full list)
- [x] 3.5 Create `x-contact-blocks` for 4 home blocks
- [x] 3.6 Update `hero-slider.blade.php`: video muted autoplay playsinline + poster; hide nav if slide count ≤ 1

## Phase 4: Pages

- [x] 4.1 Rewrite `pages/home.blade.php`: hero → hola → logos → services-preview → visit-us → contact-blocks; remove blog/IG
- [x] 4.2 Update about page: «¿Quiénes Somos?» hero, Hola split, visit-us
- [x] 4.3 Update restaurants index: «¿Qué te provoca hoy?», category grid, visit-us
- [x] 4.4 Rewrite events index: SDD hero + 4 EventOffer cards + visit-us (no Event date list); keep `events/show` unlinked
- [x] 4.5 Create `pages/services/index.blade.php`: «Nuestros servicios» + services-grid + visit-us
- [x] 4.6 Create stub Blades under `pages/static/`: FAQ, Pet Friendly, estacionamiento, Descuentos U. Lima

## Phase 5: Header / footer / routes / controllers

- [x] 5.1 Update `header.blade.php` from `config('navigation.primary')` + Reserva aquí; no Servicios
- [x] 5.2 Update `footer.blade.php` to 3 cols (nav | secondary | legal+social)
- [x] 5.3 Update `HomeController`: load HeroSlide, HomeRestaurantFeature, ServiceItem (home), ContactBlock; drop Event/IG/BlogPost
- [x] 5.4 Update `EventController@index` to pass EventOffers; keep `@show` for legacy slug
- [x] 5.5 Add `ServiceController` + routes: `/servicios`, footer stubs; keep blog routes

## Phase 6: Seeders

- [x] 6.1 `ServiceItemSeeder` — SDD service list; wire in `DatabaseSeeder`
- [x] 6.2 `EventOfferSeeder` — 4 SDD titles (Shows musicales, niños, organiza evento/fiesta)
- [x] 6.3 `ContactBlockSeeder` — 4 SDD blocks (publicidad, comerciales, servicio al cliente, trabaja con nosotros)
- [x] 6.4 Update VisitInfo seed/data to SDD «¿Nos visitas?» schedule unless existing copy is clearly better

## Phase 7: Testing / smoke

- [x] 7.1 Feature: `/` six SDD sections; assertDontSee blog/Instagram; hero nav iff 2+ slides
- [x] 7.2 Feature: `/eventos` 4 offers + visit-us; `/eventos/{slug}` still 200
- [x] 7.3 Feature: `/servicios` + FAQ/Pet/parking/U.Lima stubs + blog routes return 200
- [x] 7.4 Smoke: video autoplay + image fallback; visit-us on home/about/restaurants/events/services
- [x] 7.5 Run `php artisan test` (and `npm run build` if assets touched)
