# Admin (Filament) Specification

## Purpose

Panel administrativo Filament v3 en `/admin` para gestionar todo el contenido del sitio sin modificar código.

## Requirements

### Requirement: Hero Slide Management

The system MUST provide a Filament resource to CRUD hero slides.

#### Scenario: Admin creates a new slide

- GIVEN an authenticated admin user
- WHEN they create a HeroSlide with title, subtitle, image, sort order, and `is_active = true`
- THEN the slide appears on the homepage carousel in the specified order

#### Scenario: Admin reorders slides

- GIVEN 3 active slides with sort orders 1, 2, 3
- WHEN admin changes slide B to sort order 1
- THEN slide B appears first in the carousel

### Requirement: Restaurant Management

The system MUST provide full CRUD for restaurants with media uploads and category assignment.

#### Scenario: Admin edits restaurant

- GIVEN restaurant "Ahumare" exists
- WHEN admin updates name, description, logo, featured image, categories, WhatsApp link, and menu PDF
- THEN changes are reflected immediately on `/restaurantes/ahumare/`

#### Scenario: Admin toggles restaurant visibility

- GIVEN restaurant "Ahumare" is active
- WHEN admin sets `is_active = false` and saves
- THEN `/restaurantes/ahumare/` returns 404

### Requirement: Restaurant Category Management

The system MUST allow admins to manage filter categories.

#### Scenario: Admin creates category

- GIVEN admin creates category "Internacional" with slug `internacional`
- WHEN a restaurant is assigned this category
- THEN it appears when visitors filter by "Internacional" on `/restaurantes/`

### Requirement: Home Restaurant Grid

The system MUST allow admins to configure which restaurants appear on the homepage grid and their order.

#### Scenario: Admin features restaurants on home

- GIVEN 22 restaurants exist
- WHEN admin adds 8 restaurants to HomeRestaurantFeature with sort orders
- THEN only those 8 appear in the homepage restaurant section in specified order

### Requirement: Event Management

The system MUST provide CRUD for events with date, time, image, and description.

#### Scenario: Admin creates event

- GIVEN admin creates event "Latin Groove" with date 2026-07-20 and `is_active = true`
- WHEN visitor loads `/eventos/`
- THEN "Latin Groove" appears with day "Jue" and date "20"

### Requirement: Visit Info (Singleton)

The system MUST provide a single editable page for all "Visítanos" / contact information.

#### Scenario: Admin updates hours

- GIVEN VisitInfo has schedule JSON
- WHEN admin updates Thursday hours to "7am – 12am"
- THEN `/contacto/` and `/nosotros/` reflect the new hours

### Requirement: CTA Blocks

The system MUST allow editing the three footer CTA blocks (evento, renta, contacto).

#### Scenario: Admin updates CTA link

- GIVEN CTA block "liderar un evento" exists
- WHEN admin changes its link URL
- THEN the homepage CTA section uses the new URL

### Requirement: Site Settings

The system MUST provide a settings page for global configuration.

#### Scenario: Admin updates WhatsApp number

- GIVEN SiteSetting has WhatsApp link
- WHEN admin updates the WhatsApp URL
- THEN header "¡RESERVA AQUÍ!" button uses the new link

### Requirement: Role-Based Access

The system MUST restrict admin access to authenticated users with admin role.

#### Scenario: Unauthorized access

- GIVEN a user is not authenticated
- WHEN they navigate to `/admin`
- THEN they are redirected to the Filament login page

#### Scenario: Non-admin user

- GIVEN a user without admin role is authenticated
- WHEN they navigate to `/admin`
- THEN access is denied with HTTP 403

### Requirement: Media Management

The system MUST use Spatie Media Library for image and PDF uploads with automatic optimization.

#### Scenario: Admin uploads restaurant logo

- GIVEN admin uploads a PNG logo for a restaurant
- THEN the system stores it, generates WebP conversion, and serves optimized version on frontend
