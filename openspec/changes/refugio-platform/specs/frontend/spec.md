# Frontend Specification

## Purpose

Sitio público que replica fielmente refugiogastronomico.pe con componentes Blade modulares y diseño responsive.

## Requirements

### Requirement: Hero Slider

The system MUST render a fullscreen carousel on the homepage with configurable slides from the CMS.

#### Scenario: Visitor views homepage hero

- GIVEN at least one active HeroSlide exists
- WHEN a visitor loads `/`
- THEN a fullscreen slider displays with title, subtitle, background image and optional CTA
- AND slides auto-rotate every 5 seconds with manual navigation dots/arrows

#### Scenario: No active slides

- GIVEN no active HeroSlide records exist
- WHEN a visitor loads `/`
- THEN a default static hero with site slogan is displayed

### Requirement: Restaurant Grid with Filters

The system MUST display a filterable grid of restaurants on `/restaurantes/`.

#### Scenario: Filter by category

- GIVEN restaurants exist in categories "Peruana" and "Bar"
- WHEN a visitor clicks the "Peruana" filter pill
- THEN only restaurants tagged "Peruana" are shown
- AND the "Peruana" pill has active visual state

#### Scenario: View all restaurants

- GIVEN filtered results are displayed
- WHEN a visitor clicks "Todos"
- THEN all active restaurants are shown

### Requirement: Restaurant Detail Page

The system MUST render individual restaurant pages at `/restaurantes/{slug}/`.

#### Scenario: View restaurant detail

- GIVEN a restaurant "Ahumare" with slug `ahumare` exists and is active
- WHEN a visitor navigates to `/restaurantes/ahumare/`
- THEN the page shows name, featured image, map embed, and "Pide ahora" CTA
- AND a "Regresar" link returns to `/restaurantes/`

#### Scenario: Inactive restaurant

- GIVEN restaurant slug `ahumare` has `is_active = false`
- WHEN a visitor navigates to `/restaurantes/ahumare/`
- THEN HTTP 404 is returned

### Requirement: Events Listing

The system MUST display events on `/eventos/` sorted by date descending.

#### Scenario: View upcoming events

- GIVEN 3 active future events exist
- WHEN a visitor loads `/eventos/`
- THEN events display as cards with day abbreviation, date number, and title
- AND each card links to `/eventos/{slug}/`

### Requirement: Visit Us Section

The system MUST render location and contact information from the VisitInfo singleton.

#### Scenario: View contact page

- GIVEN VisitInfo has address, hours, phone, and map embed configured
- WHEN a visitor loads `/contacto/`
- THEN address, hours, phone, email, and Google Maps embed are displayed
- AND three CTA blocks (evento, renta, contacto) are shown at the bottom

### Requirement: Global Header and Navigation

The system MUST render a consistent header across all public pages.

#### Scenario: Desktop navigation

- GIVEN viewport width ≥ 1024px
- WHEN any public page loads
- THEN header shows logo, nav links (Restaurantes, Eventos, Nosotros, Blog, Contacto), and WhatsApp CTA
- AND header has gradient dark background with absolute positioning over hero

#### Scenario: Mobile navigation

- GIVEN viewport width < 1024px
- WHEN a visitor taps the hamburger menu
- THEN an off-canvas panel opens with all nav links and social icons

### Requirement: Responsive Design

The system MUST be fully functional at breakpoints 375px, 768px, 1024px, and 1440px.

#### Scenario: Mobile layout

- GIVEN viewport width is 375px
- WHEN any page loads
- THEN content stacks vertically without horizontal overflow
- AND touch targets are minimum 44×44px

### Requirement: SEO

The system MUST generate proper meta tags and structured data.

#### Scenario: Homepage SEO

- GIVEN SiteSetting has SEO title and description
- WHEN `/` is rendered
- THEN `<title>`, `<meta description>`, Open Graph tags, and schema.org Organization JSON-LD are present
