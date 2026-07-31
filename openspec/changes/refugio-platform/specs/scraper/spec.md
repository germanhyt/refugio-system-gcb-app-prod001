# Scraper Specification

## Purpose

Comando Artisan para importar contenido, imágenes y metadatos desde refugiogastronomico.pe (WordPress) al CMS Laravel.

## Requirements

### Requirement: Sitemap-Based Discovery

The system MUST discover all importable URLs from WordPress sitemaps.

#### Scenario: Discover all content URLs

- GIVEN sitemaps at `/wp-sitemap.xml`, `/restaurantes-sitemap.xml`, `/eventos-sitemap.xml`
- WHEN `php artisan refugio:import --discover` runs
- THEN a list of 22 restaurant URLs, 8 event URLs, and static pages is output
- AND no duplicate URLs are listed

### Requirement: Restaurant Import

The system MUST scrape and upsert restaurant data by slug.

#### Scenario: Import all restaurants

- GIVEN the source site has 22 restaurant pages
- WHEN `php artisan refugio:import restaurants` runs
- THEN 22 Restaurant records are created or updated (upsert by slug)
- AND each record has name, slug, featured image downloaded to storage, and description HTML

#### Scenario: Re-import is idempotent

- GIVEN restaurants were already imported
- WHEN `php artisan refugio:import restaurants` runs again
- THEN no duplicate records are created
- AND existing records are updated with latest source data

### Requirement: Event Import

The system MUST import events with date parsing from source pages.

#### Scenario: Import events

- GIVEN 8 event pages exist on source site
- WHEN `php artisan refugio:import events` runs
- THEN Event records are created with title, slug, date, and image

### Requirement: Image Download

The system MUST download and store images locally with organized paths.

#### Scenario: Download restaurant images

- GIVEN restaurant page has featured image at `wp-content/uploads/...`
- WHEN scraper processes the page
- THEN image is saved to `storage/app/public/restaurants/{slug}/featured.{ext}`
- AND media library record is attached to the Restaurant model

#### Scenario: Skip existing images

- GIVEN image already exists in storage with matching checksum
- WHEN scraper re-runs
- THEN image is not re-downloaded (unless `--force` flag is used)

### Requirement: Static Page Import

The system MUST import content for contact, about, and legal pages.

#### Scenario: Import visit info

- GIVEN `/contacto/` and `/nosotros/` contain address, hours, phone, email
- WHEN `php artisan refugio:import pages` runs
- THEN VisitInfo singleton is populated with parsed contact data

### Requirement: Hero Slide Import

The system MUST extract homepage slider content.

#### Scenario: Import hero slides

- GIVEN homepage has Revolution Slider / Elementor carousel
- WHEN `php artisan refugio:import hero` runs
- THEN HeroSlide records are created with titles, subtitles, and background images

### Requirement: Error Handling

The system MUST log failures and continue processing remaining items.

#### Scenario: Single page failure

- GIVEN one restaurant page returns HTTP 500
- WHEN scraper processes all restaurants
- THEN the failed URL is logged to `storage/logs/scraper.log`
- AND remaining restaurants are still imported
- AND exit code is non-zero with summary of failures

### Requirement: Rate Limiting

The system MUST respect the source server with configurable delay between requests.

#### Scenario: Polite scraping

- GIVEN default delay is 500ms
- WHEN scraper processes 22 restaurant pages
- THEN at least 500ms passes between each HTTP request
- AND User-Agent identifies the scraper as "RefugioMigrator/1.0"

### Requirement: Full Import Command

The system MUST provide a single command to run all import steps.

#### Scenario: Full import

- WHEN `php artisan refugio:import --all` runs
- THEN restaurants, events, pages, hero slides, and images are imported in sequence
- AND a summary report shows counts per entity type
