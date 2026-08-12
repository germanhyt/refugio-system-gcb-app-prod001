# Proposal: Public site restructure (web-restructure)

## Intent

Restructure the public site to match `SDD.md`. Supersedes conflicting frontend IA from `refugio-platform` (nav, home, events listing).

## Scope

### In Scope
- Header: Nosotros / Restaurantes / Eventos + «Reserva aquí»; no Servicios
- Footer: 3 cols (nav | secondary | legal+social)
- Home: video hero; Hola; logo carousel; services preview + Ver más; visit-us; 4 contact blocks; no blog/Instagram
- Nosotros / Restaurantes / Eventos / Servicios per SDD
- `/eventos` = 4 offer cards; `Event` + `/eventos/{slug}` kept legacy (unlinked)
- Light CMS: `ServiceItem` + `EventOffer` Filament + seeders
- Footer col2 placeholders (FAQ, Pet Friendly, estacionamiento, Descuentos U. Lima); blog keeps routes
- Shared Blade: visit-us, hola, services-grid, contact-blocks, logo carousel

### Out of Scope
- Page-builder; deleting Event admin/slugs; platform SEO/infra; final placeholder copy

## Approach

**Approach 1 — modular Blade + light CMS.** Reusable components; extend `HeroSlide` for video; reuse `VisitInfo` + `HomeRestaurantFeature`; Filament/seed for services and offers; centralized nav.

## Affected Areas

- header / footer — Modified — SDD nav + 3-col footer
- home + HomeController — Modified — new composition; drop IG/blog/events
- hero-slider / HeroSlide — Modified — video + conditional controls
- about / restaurants / events — Modified — SDD layouts; events → offers
- /servicios, ServiceItem, EventOffer — New — page + CMS + seeders
- Footer placeholders — New — static Blade stubs
- refugio-platform frontend specs — Superseded — nav/home/events IA

**Modules**: Blade, Filament, models/migrations/seeders, routes/web.php.  
**Legal**: Owned/licensed assets only — no new WP scraping.

## Risks

- Overlap refugio-platform (High) — explicit supersede of frontend deltas
- Orphan /eventos/{slug} (Med) — keep routes; omit from primary IA
- Hero video / autoplay (Med) — muted autoplay + image fallback
- Empty footer stubs (Med) — ship placeholders; fill later

## Rollback Plan

Revert Blade/route/controller commits; roll back new migrations; restore prior home sections.

## Dependencies

- Autopilot locks (IG off, offer cards, light CMS, placeholders, no Servicios in header)
- Supersede note vs refugio-platform before verify/archive of that change

## Success Criteria

- [ ] Header/footer + Reserva aquí match SDD
- [ ] Home per SDD; no blog/Instagram
- [ ] Eventos = 4 offers; Event slug/admin unlinked but live
- [ ] Servicios + home preview editable in Filament
- [ ] visit-us on five surfaces; footer stubs + blog links work
