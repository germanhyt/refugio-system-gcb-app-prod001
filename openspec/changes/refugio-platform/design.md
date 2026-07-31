# Design: Plataforma Refugio Gastronómico

## Technical Approach

Monolito Laravel 11 con tres capas: **Scraper** (importación one-shot), **CMS Filament** (gestión de contenido), **Frontend Blade** (renderizado público). Los modelos Eloquent son la fuente de verdad post-importación.

## Architecture Decisions

| Decision | Choice | Alternatives | Rationale |
|----------|--------|-------------|-----------|
| Framework | Laravel 11 + Filament v3 | WordPress headless, Strapi | Filament = admin rápido y nativo PHP; usuario lo solicitó |
| Frontend | Blade + Livewire 3 + Alpine.js | Inertia+Vue, Next.js | Simplicidad, SEO nativo, sin SPA overhead |
| CSS | Tailwind CSS 3 | Bootstrap, CSS puro | Utility-first; fácil replicar tokens del audit |
| Carruseles | Swiper.js | Glide.js, Slick | Estándar en proyectos Laravel; touch-friendly |
| Media | Spatie Media Library | Laravel Storage directo | Conversiones WebP, colecciones, admin integration |
| Slugs | Spatie Sluggable | Manual | Auto-generación desde nombre |
| Scraper | Symfony DomCrawler + Http Client | Goutte (deprecated) | Nativo Symfony, mantenido |
| DB | MySQL 8 | PostgreSQL | Convención Laravel hosting Perú |

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    refugiogastronomico.pe               │
│                   (WordPress - fuente)                  │
└──────────────────────┬──────────────────────────────────┘
                       │ HTTP scrape (one-shot)
                       ▼
┌─────────────────────────────────────────────────────────┐
│              php artisan refugio:import                 │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌───────────┐  │
│  │Restaurants│ │ Events  │ │  Pages   │ │Hero Slides│  │
│  └──────────┘ └──────────┘ └──────────┘ └───────────┘  │
└──────────────────────┬──────────────────────────────────┘
                       │ upsert
                       ▼
┌─────────────────────────────────────────────────────────┐
│                   MySQL Database                        │
│  restaurants · events · hero_slides · visit_info · ...  │
└──────────┬──────────────────────────┬───────────────────┘
           │                          │
           ▼                          ▼
┌─────────────────────┐   ┌──────────────────────────────┐
│  Frontend (Blade)   │   │  Admin Panel (Filament v3)   │
│  /                  │   │  /admin                      │
│  /restaurantes      │   │  CRUD todos los módulos      │
│  /eventos           │   │  Media uploads               │
│  /contacto          │   │  Site settings               │
└─────────────────────┘   └──────────────────────────────┘
```

## Design System (Tokens)

```css
/* resources/css/tokens.css */
:root {
  --color-primary: #1a1a1a;
  --color-accent: #c8a951;        /* dorado CTA */
  --color-bg-dark: rgba(0,0,0,0.52);
  --color-text-light: #ffffff;
  --color-text-muted: #999999;
  --font-primary: 'DM Sans', sans-serif;  /* aproximar --primary-ff */
  --container-max: 1440px;
  --header-height: 80px;
  --border-radius-card: 12px;
  --transition-default: 300ms ease;
}
```

### Breakpoints

| Name | Min-width | Uso |
|------|-----------|-----|
| mobile | 0 | Stack vertical, hamburger menu |
| tablet | 768px | Grid 2 columnas |
| desktop | 1024px | Nav horizontal, grid 3-4 cols |
| wide | 1440px | Container max-width |

## Data Flow: Restaurant Edit

```
Admin (Filament) ──save──▶ Restaurant Model ──▶ Media Library
                                │
                                ▼
                         Cache::forget('restaurants')
                                │
                                ▼
Frontend /restaurantes/{slug} ◀── RestaurantController::show()
```

## Directory Structure

```
app/
├── Console/Commands/
│   ├── RefugioImportCommand.php
│   ├── ImportRestaurantsCommand.php
│   ├── ImportEventsCommand.php
│   └── ImportPagesCommand.php
├── Filament/
│   ├── Resources/
│   │   ├── HeroSlideResource.php
│   │   ├── RestaurantResource.php
│   │   ├── RestaurantCategoryResource.php
│   │   ├── EventResource.php
│   │   ├── CtaBlockResource.php
│   │   └── NewsletterSubscriberResource.php
│   └── Pages/
│       ├── SiteSettings.php
│       └── VisitInfo.php
├── Http/Controllers/
│   ├── HomeController.php
│   ├── RestaurantController.php
│   ├── EventController.php
│   ├── ContactController.php
│   └── NewsletterController.php
├── Models/
│   ├── Restaurant.php
│   ├── RestaurantCategory.php
│   ├── Event.php
│   ├── HeroSlide.php
│   ├── VisitInfo.php
│   ├── HomeRestaurantFeature.php
│   ├── CtaBlock.php
│   ├── SiteSetting.php
│   └── NewsletterSubscriber.php
└── Services/
    └── Scraper/
        ├── SitemapParser.php
        ├── RestaurantScraper.php
        ├── EventScraper.php
        ├── PageScraper.php
        └── ImageDownloader.php

resources/views/
├── layouts/
│   └── app.blade.php
├── components/
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── hero-slider.blade.php
│   ├── restaurant-card.blade.php
│   ├── event-card.blade.php
│   ├── cta-triple.blade.php
│   ├── category-filter.blade.php
│   └── map-embed.blade.php
├── pages/
│   ├── home.blade.php
│   ├── restaurants/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── events/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── contact.blade.php
│   └── about.blade.php
```

## Database Schema (ER)

```
restaurant_categories ──M:N── restaurants
restaurants ──1:N── home_restaurant_features
hero_slides (standalone)
events (standalone)
visit_info (singleton, id=1)
site_settings (singleton, id=1)
cta_blocks (standalone)
newsletter_subscribers (standalone)
```

## Scraper Sequence

```
1. SitemapParser::discover() → URL[]
2. For each restaurant URL:
   a. Http::get(url) → HTML
   b. DomCrawler → extract name, description, images
   c. ImageDownloader → storage
   d. Restaurant::updateOrCreate(['slug' => $slug], $data)
3. Report summary
```

## Testing Strategy

| Layer | What | Approach |
|-------|------|----------|
| Unit | Scraper parsers, slug generation | PHPUnit + mocked HTTP |
| Feature | Routes return 200, 404 for inactive | Pest feature tests |
| Feature | Filament CRUD operations | Livewire test helpers |
| Browser | Visual regression (optional) | Laravel Dusk / manual QA checklist |

## Migration / Rollout

1. **Fase 1**: Scaffold Laravel + Filament + migrations
2. **Fase 2**: Scraper import completo
3. **Fase 3**: Frontend componentes (home → restaurantes → eventos → contacto)
4. **Fase 4**: Admin polish + QA visual
5. **Fase 5**: DNS switch (fuera de scope técnico)

No migration required for existing data — greenfield project.

## Open Questions

- [ ] ¿Fuente tipográfica exacta del tema Rey? (usar DM Sans como aproximación hasta confirmar)
- [ ] ¿Blog en fase 1 o fase 2?
- [ ] ¿Integración Instagram API o módulo manual de posts destacados?
- [ ] ¿Hosting objetivo? (define CI/CD y env vars)
