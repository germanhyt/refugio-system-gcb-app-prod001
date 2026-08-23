# Spec Delta: GEO — Admin CMS

## Requirement: El CMS no requiere cambios para Fase 1

### Scenario: Los campos SEO existentes siguen funcionando
- **Given** un administrador en `/admin`
- **When** edita `SiteSetting` (seo_title, seo_description, redes sociales) o un `Restaurant` (meta_title, meta_description, google_maps_url, redes)
- **Then** los valores MUST persistir y MUST ser usados por el `SeoService` al generar JSON-LD
- **And** no MUST haber migración de BD nueva en Fase 1

## Requirement: Atributos dietéticos y de ocasión (Fase 2 — fuera de alcance de implementación inicial)

### Scenario: Campos futuros para amenidades dietéticas
- **Given** la decisión de añadir atributos (vegano, sin gluten, familiar, romántico) al parque y conceptos
- **When** se ejecute la Fase 2
- **Then** se añadirán campos editables en Filament a `SiteSetting` y `Restaurant`
- **And** el `SeoService` los emitirá como `amenityFeature` adicionales
- **Note:** Esta spec documenta la intención; la implementación se detalla en el `tasks.md` de Fase 2 (no se ejecuta ahora).

## Requirement: Regeneración de sitemap tras cambios

### Scenario: Comando de sitemap disponible para el admin/ops
- **Given** contenido actualizado en el CMS (restaurantes, eventos, páginas)
- **When** se ejecuta `php artisan refugio:sitemap`
- **Then** `public/sitemap.xml` MUST regenerarse con el contenido actual
- **And** el comando MAY ejecutarse manualmente o programarse (cron/CI) — no se acopla al guardado del CMS en Fase 1
