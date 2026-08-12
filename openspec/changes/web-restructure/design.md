# Design: Public site restructure (web-restructure)

## Technical Approach

Modular Blade + light CMS (proposal Approach 1 / `SDD.md`). Shared section components; extend Spatie/Filament (`HeroSlide`, `HomeRestaurantFeature`, `VisitInfo`); add `ServiceItem` + `EventOffer` + `ContactBlock`; centralize nav. Legacy `Event` + `/eventos/{slug}` stay routable, unlinked from primary IA.

## Architecture Decisions

| Decision | Options | Tradeoff | Choice |
|----------|---------|----------|--------|
| Nav source | Dup arrays; View Composer; config PHP | Composer already shares settings/visit; config is static | **`config/navigation.php`** → header, footer, overlay |
| Visit-us | Per-page copy; component on VisitInfo; CMS page | VisitInfo + composer already global | **`x-visit-us`** from `$visitInfo` |
| Services | Hardcode; ServiceItem; page-builder | Light CMS locked | **`ServiceItem`** Filament (Contenido), `active`/`ordered` |
| Eventos | Event dates; static Blade; EventOffer | Autopilot: 4 offers; slug legacy | **`EventOffer`** seed 4; index→offers; show unchanged |
| Hero video | New model; extend HeroSlide; hardcode | Spatie on HeroSlide | **`media_type` + `background_video`**; image = poster/fallback |
| Contact 4-up | CtaBlock; ContactBlock; Blade seed | CtaBlock = 3 contact-page types | **`ContactBlock`** model |
| Logos | New table; HomeRestaurantFeature | Already Filament | **Reuse HomeRestaurantFeature** in `x-logo-carousel` |
| Footer col2 | Scraper; Filament; Blade stubs | Autopilot placeholders | **Static Blade stubs** + routes |
| IG/blog home | Keep; remove | Autopilot off | **Remove** from home + controller queries |

## Data Model

**`service_items`**: title, icon_key (nullable) or Spatie `icon`, description?, sort_order, show_on_home, is_active.

**`event_offers`**: title, slug unique?, summary?, cta_text/url?, sort_order, is_active; optional Spatie `cover`. Seed 4 SDD titles.

**`contact_blocks`**: title, body (text/JSON lines), phones/emails?, sort_order, is_active. Seed 4 SDD blocks.

**`hero_slides` delta**: `media_type` (`image`|`video`); collection `background_video`; keep `background_image` as poster/fallback. Swiper nav only if active count > 1; video muted autoplay playsinline + poster.

**Reuse**: VisitInfo, HomeRestaurantFeature, Event (admin/show only).

## Data Flow (home)

```
GET / → HomeController
  HeroSlide::active()->ordered()->with(media)
  HomeRestaurantFeature::active()->ordered()->with(restaurant.media)
  ServiceItem::active()->showOnHome()->ordered()
  ContactBlock::active()->ordered()
  VisitInfo via View Composer
       ↓
home: hero-slider → hola → logo-carousel → services-preview
      → visit-us → contact-blocks
```

No Event / Instagram / BlogPost on home.

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `config/navigation.php` | Create | primary / footer_secondary / footer_legal |
| `ServiceItem`, `EventOffer`, `ContactBlock` + migrations/seeders | Create | Models + scopes + seed |
| Filament Resources for three models (+ Pages) | Create | Contenido group, reorderable |
| `HeroSlide` + `HeroSlideResource` + migration | Modify | media_type, video collection, conditional form |
| `HomeController`, `EventController` | Modify | New home payload; events index→offers |
| `ServiceController` + `routes/web.php` | Create/Modify | `/servicios` + footer stubs |
| `header` / `footer` / `hero-slider` | Modify | SDD nav; 3-col footer; video + conditional nav |
| `x-{hola-section,visit-us,services-preview,services-grid,logo-carousel,contact-blocks}` | Create | Shared Blade |
| `pages/{home,about,restaurants/index,events/index}` | Modify | SDD layouts + visit-us |
| `pages/services/index` + `pages/static/*` | Create | Servicios + FAQ/Pet/parking/U.Lima stubs |

## Interfaces

```php
// config/navigation.php
'primary' => [['label'=>'Nosotros','route'=>'about'], ...],
'footer_secondary' => [['label'=>'blog','route'=>'blog.index'], ...],
'footer_legal' => [['label'=>'…','route'=>'legal.terms'], ...],
```

Slide contract: `media_type`, poster URL, optional video URL; nav iff `$slides->count() > 1`.

## Testing Strategy

| Layer | What | Approach |
|-------|------|----------|
| Feature | `/` no IG/blog/events; services preview | assertSee/DontSee |
| Feature | `/eventos` offers; `/servicios` + stubs 200 | Feature tests |
| Manual | Video autoplay; single slide hides arrows | Browser |

## Migration / Rollout

Migrate+seed new tables + hero column; deploy Blade/controllers/routes atomically. Rollback: revert code + migrate:rollback. No flags. Declare supersede of `refugio-platform` frontend IA before that change’s verify/archive.

## Open Questions

- [ ] SDD schedule «1:00/3:00 p.m.» vs likely a.m. — confirm before VisitInfo seed
- [ ] Specs may land under `specs/` in parallel — reconcile in tasks if added
