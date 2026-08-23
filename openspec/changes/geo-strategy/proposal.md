# Proposal: Estrategia GEO para Refugio Gastronómico

## Intent

Refugio Gastronómico (food park de ubicación única en Surco, Lima, con ~21 conceptos gastronómicos) es hoy prácticamente invisible para los motores de IA generativos (ChatGPT, Perplexity, Google AI Overviews, Gemini, Claude). El sitio tiene fundamentos SEO básicos (títulos, meta, Open Graph) pero carece de los tres activos que más pesan en GEO: datos estructurados (Schema.org/JSON-LD), contenido Q&A citable y señales de entidad distribuidas. Esta change convierte el material factual ya existente en el CMS Filament en infraestructura legible para IA, con resultados medibles en 60–90 días.

## Scope

### In Scope
- `SeoService` que genere JSON-LD dinámico (`Organization`/`FoodEstablishment` del parque + `FoodEstablishment` por concepto, con `GeoCoordinates`, `OpeningHoursSpecification`, `amenityFeature`, `servesCuisine`, `sameAs`, `priceRange`).
- `FAQPage` schema + FAQ maestra answer-first (10–15 Q&A) y FAQ por concepto (top 5–8).
- Comando `GenerateSitemap` (XML) + URLs canónicas + Twitter Cards en el layout.
- Resolver ruta `/contacto` (404 actual) y unificar NAP/emails en todo el sitio y CMS.
- `llms.txt` experimental en raíz (claims curados y actualizados).
- Marcar contenido estático existente (pet-friendly, parqueo, descuentos U. Lima) con schema relevante.
- Guías de barrio ("Dónde comer en Surco", "Food parks en Lima") para citación por localidad.
- Dashboard de KPIs de citación (frecuencia, share of voice, precisión) + atribución GA4 de tráfico de IA.

### Out of Scope
- Google Business Profile API (gestión operativa, no solo código) — coordinar externamente.
- Campaña de reseñas y outreach editorial (procesos de marketing, no código).
- Migración del scraper ni cambios en el modelo de datos de restaurantes.
- Multi-idioma / `hreflang` (sitio monolingüe `es_PE`).

## Approach

Capa GEO sobre los fundamentos SEO existentes. Aprovechar que `robots.txt` ya permite crawlers de IA (`User-agent: * / Disallow:`): el problema es de contenido estructurado y señales de entidad, no de acceso. Modelo de entidad: parque como `FoodEstablishment` con `subOrganization` → 21 conceptos (sin NAP propio, correcto para food hall). Patrón `@id` para enlazar nodos y resolver entidad en el Knowledge Graph. Schema que refleje contenido real (no `FAQPage` en páginas sin FAQ). Basado en investigación 2026: Google (mayo 2026) confirma que no hay schema obligatorio para AI Overviews y `llms.txt` no afecta Google — schema es rol indirecto (resolución de entidad), no palanca directa de citación. Detalle completo en `exploration.md`.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `app/Services/SeoService.php` | New | Generación dinámica de JSON-LD desde modelos |
| `resources/views/layouts/app.blade.php` | Modified | `@stack('json-ld')`, canonicals, Twitter Cards |
| `app/Console/Commands/GenerateSitemap.php` | New | Sitemap XML |
| `routes/web.php` | Modified | Resolver `/contacto` |
| `public/llms.txt` | New | Manifiesto experimental para IA |
| `resources/views/pages/faq.blade.php` | New/Modified | FAQ maestra + `FAQPage` schema |
| `resources/views/restaurants/show.blade.php` | Modified | JSON-LD por concepto |
| `database/seeders/` + CMS Filament | Modified | Unificar NAP/emails, atributos dietéticos/ocasión |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|-----------|
| Schema no garantiza citación (Google mayo 2026) | High | No tratar schema como palanca directa; combinar con contenido answer-first + GBP + menciones de terceros |
| `llms.txt` estadísticamente insignificante para Google | High | Mantenerlo solo como infraestructura de bajo costo para agentes; no depender de él |
| `/contacto` 404 erosiona confianza y embudo | Med | Resolver en Semana 3 antes de escalar contenido |
| Inconsistencia de emails (hola@, leilah@gcb.pe, mike@gcb.pe) | Med | Unificar en CMS y todo el sitio en Semana 3 |
| 21 conceptos sin NAP propio (mal modelado) | Low | No inventar direcciones; modelar como `subOrganization` del parque |

## Rollback Plan

Todos los cambios son aditivos (JSON-LD, sitemap, canonicals, `llms.txt`). Revertir: eliminar `SeoService`, quitar `@stack('json-ld')` del layout, borrar `GenerateSitemap` y `public/llms.txt`, restaurar `/contacto` a su estado anterior. No hay migraciones destructivas: el cambio no altera schema de BD existente (solo seeders/CMS de NAP). Rollback seguro por git.

## Dependencies

- Acceso al Google Business Profile (gestión activa, externa al código) — coordinar con marketing.
- Responsable de contenido para FAQ maestra y guías de barrio.
- Línea base de citación en 10 consultas objetivo antes de iniciar (test en ChatGPT/Perplexity/Gemini/AI Overviews).

## Success Criteria

- [ ] JSON-LD del parque + 21 conceptos válido (Schema.org validator, sin errores).
- [ ] Sitemap XML generado y servido; canonicals en todas las páginas.
- [ ] `/contacto` responde 200 o redirige correctamente; NAP unificado 100%.
- [ ] Refugio citado por nombre en ≥ 8 de 10 consultas objetivo en motores de IA (60–90 días).
- [ ] Precisión de citación (hechos correctos) ≥ 90%.
- [ ] Share of voice ≥ 40% en consultas "food park / parque gastronómico Lima".
- [ ] Tráfico atribuido a IA medible en GA4.
