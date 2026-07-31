# Proposal: Plataforma Refugio Gastronómico

## Intent

Construir una réplica profesional y administrable del sitio refugiogastronomico.pe usando Laravel + Filament PHP, con scraper inicial para importar contenido y un CMS que permita editar banners, grid de restaurantes, detalle de restaurantes, eventos y sección Visítanos sin tocar código.

## Scope

### In Scope
- Proyecto Laravel 11 con Filament v3 (panel `/admin`)
- Scraper Artisan para importar páginas, restaurantes, eventos, imágenes y metadatos
- Frontend público pixel-faithful: Home, Restaurantes, Detalle restaurante, Eventos, Contacto/Visítanos, Nosotros
- CMS completo: Hero slides, restaurantes + categorías, eventos, info de visita, CTAs, configuración global
- Responsive: mobile, tablet, desktop (breakpoints 768px, 1024px, 1440px)
- SEO: meta tags, Open Graph, sitemap XML, schema.org Organization

### Out of Scope
- E-commerce / pedidos online (solo CTA WhatsApp)
- Blog completo con editor rico (fase 2)
- Instagram API live feed (usar módulo manual de posts destacados)
- Multi-idioma
- Convocatorias y libro de reclamaciones (fase 2)

## Approach

Monolito Laravel: scraper one-shot importa datos del WordPress origen → Filament admin gestiona todo el contenido → Blade/Livewire renderiza frontend con componentes modulares y design tokens del audit.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `app/Models/` | New | 10+ Eloquent models |
| `app/Filament/Resources/` | New | Admin CRUD resources |
| `app/Console/Commands/` | New | Scraper import commands |
| `resources/views/` | New | Public frontend templates |
| `database/migrations/` | New | Full schema |
| `routes/web.php` | New | Public routes |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Asset copyright | Med | Internal migration only; legal clearance before prod |
| Visual parity gaps | Med | Design token doc + visual QA checklist |
| Scraper breakage | Low | WP sitemap-based; graceful fallbacks |

## Rollback Plan

1. Scraper es idempotente — re-ejecutar no duplica (upsert por slug)
2. Migraciones reversibles con `migrate:rollback`
3. Frontend desacoplado del admin — desactivar rutas públicas sin afectar CMS
4. Assets en `storage/` — backup antes de re-import

## Dependencies

- PHP 8.2+, Composer, Node.js 20+
- MySQL 8 or PostgreSQL 15
- Google Maps embed API (opcional, iframe funciona sin key)

## Success Criteria

- [ ] Home replica las 6 secciones principales del sitio origen
- [ ] 22 restaurantes importados con imagen, slug y categoría
- [ ] Admin edita banner, restaurantes, eventos y Visítanos sin código
- [ ] Lighthouse Performance ≥ 80, Accessibility ≥ 90
- [ ] Responsive funcional en 375px, 768px, 1440px
