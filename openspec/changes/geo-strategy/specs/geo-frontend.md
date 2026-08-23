# Spec Delta: GEO — Frontend público

## Requirement: Datos estructurados del parque (JSON-LD)

### Scenario: Home emite schema del parque
- **Given** el sitio público está renderizado
- **When** un crawler o motor de IA solicita `/`
- **Then** el `<head>` MUST contener un `<script type="application/ld+json">` con `@type` `FoodEstablishment`
- **And** el objeto MUST incluir `@id` terminado en `#refugio`, `name` "Refugio Gastronómico", `url`, `address` (PostalAddress con `streetAddress`, `addressLocality` "Santiago de Surco", `addressRegion` "Lima", `addressCountry` "PE"), `geo` (GeoCoordinates con lat/lng), `sameAs` (Instagram, Facebook, TikTok no vacíos), `amenityFeature` (Pet-friendly, Estacionamiento, Música en vivo, Bosque Mágico) y `priceRange`
- **And** el JSON MUST ser válido (parseable sin error)

### Scenario: Coordenadas y dirección desde VisitInfo
- **Given** `VisitInfo::current()` con `address` "Av. Javier Prado Este 4492"
- **When** se genera el JSON-LD del parque
- **Then** `address.streetAddress` MUST ser "Av. Javier Prado Este 4492"
- **And** `geo.latitude` MUST ser -12.0842658 y `geo.longitude` MUST ser -76.9734978 (defaults)

## Requirement: Schema por concepto de restaurante

### Scenario: Detalle de restaurante emite FoodEstablishment
- **Given** un restaurante activo con slug `cavenecia`
- **When** se solicita `/restaurantes/cavenecia`
- **Then** el `<head>` MUST contener JSON-LD con `@type` `FoodEstablishment`, `name` "Cavenecia", `url` absoluta a la página, `servesCuisine` (lista de categorías del restaurante) y `parentOrganization` con `@id` `#refugio`
- **And** si el restaurante tiene `google_maps_url`, MUST incluir `hasMap`
- **And** si tiene `sameAs` (Instagram/TikTok/website no vacíos), MUST incluirlos
- **And** si tiene `menu_pdf`, MUST incluir `menu` con la URL absoluta

### Scenario: BreadcrumbList en detalle de restaurante
- **Given** la página de detalle de un restaurante
- **When** se renderiza
- **Then** el `<head>` MUST contener JSON-LD `@type` `BreadcrumbList` con items Home → Restaurantes → [Nombre del restaurante]

## Requirement: FAQPage en preguntas frecuentes

### Scenario: Página de FAQ emite FAQPage
- **Given** la ruta `/preguntas-frecuentes` con bloque `type=faq` (desde `StaticPage` o `config/static-pages.php`)
- **When** se renderiza la página
- **Then** el `<head>` MUST contener JSON-LD `@type` `FAQPage` con `mainEntity` array de `Question`/`Answer` por cada par Q/A visible
- **And** cada `Question` MUST tener `name` (la pregunta) y `acceptedAnswer.text` (la respuesta)

## Requirement: Canonical y Twitter Cards globales

### Scenario: Toda página tiene canonical
- **Given** cualquier página pública renderizada
- **When** se inspecciona el `<head>`
- **Then** MUST contener `<link rel="canonical" href="{URL absoluta actual}">`

### Scenario: Twitter Cards presentes
- **Given** cualquier página pública
- **When** se inspecciona el `<head>`
- **Then** MUST contener `twitter:card` (summary_large_image), `twitter:title` y `twitter:description` (usando los mismos valores que OG)

## Requirement: Sitemap XML

### Scenario: Sitemap servido
- **Given** `public/sitemap.xml` generado por `php artisan refugio:sitemap`
- **When** se solicita `GET /sitemap.xml`
- **Then** la respuesta MUST ser 200 con `Content-Type` `application/xml`
- **And** el XML MUST incluir `<urlset>` con entradas para home, `/restaurantes`, cada restaurante activo, `/eventos`, `/servicios`, `/nosotros`, `/preguntas-frecuentes`, `/blog`

### Scenario: robots.txt referencia el sitemap
- **Given** `public/robots.txt`
- **When** se lee
- **Then** MUST contener una línea `Sitemap:` con la URL absoluta del sitemap
- **And** MUST seguir permitiendo todos los crawlers (incluidos bots de IA)

## Requirement: llms.txt

### Scenario: llms.txt accesible
- **Given** `public/llms.txt`
- **When** se solicita `GET /llms.txt`
- **Then** la respuesta MUST ser 200 con contenido en Markdown plano
- **And** el contenido MUST describir el parque, sus conceptos y enlaces clave (home, restaurantes, FAQ, contacto)
