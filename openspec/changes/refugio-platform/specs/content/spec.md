# Content Model Specification

## Purpose

Define el modelo de datos y relaciones para todo el contenido administrable del sitio.

## Requirements

### Requirement: Restaurant Entity

The system MUST store restaurants with the following schema.

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| id | bigint | auto | PK |
| name | string(255) | yes | Display name |
| slug | string(255) | yes | Unique, URL-safe |
| description | text | no | HTML content |
| whatsapp_url | string(500) | no | Order CTA link |
| google_maps_url | string(500) | no | Map link |
| menu_pdf | media | no | PDF menu file |
| logo | media | no | Brand logo |
| featured_image | media | no | Hero/card image |
| is_active | boolean | yes | Default true |
| sort_order | int | no | Manual ordering |
| meta_title | string(255) | no | SEO |
| meta_description | text | no | SEO |
| created_at / updated_at | timestamps | auto | |

#### Scenario: Restaurant with categories

- GIVEN restaurant "Ahumare" exists
- WHEN assigned categories "Internacional" and "Rápida"
- THEN `restaurant_category` pivot table links all three records

### Requirement: RestaurantCategory Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| name | string(100) | yes |
| slug | string(100) | yes, unique |
| sort_order | int | no |
| is_active | boolean | yes |

### Requirement: Event Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| title | string(255) | yes |
| slug | string(255) | yes, unique |
| event_date | date | yes |
| event_time | time | no |
| description | text | no |
| featured_image | media | no |
| is_active | boolean | yes |

#### Scenario: Event date display

- GIVEN event with date 2026-07-20 (Thursday)
- WHEN rendered on frontend
- THEN day abbreviation is "Jue" and day number is "20"

### Requirement: HeroSlide Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| title | string(255) | yes |
| subtitle | string(500) | no |
| description | text | no |
| background_image | media | yes |
| cta_text | string(100) | no |
| cta_url | string(500) | no |
| sort_order | int | yes |
| is_active | boolean | yes |

### Requirement: VisitInfo Singleton

| Field | Type | Required |
|-------|------|----------|
| id | bigint | 1 (singleton) |
| address | string(500) | yes |
| schedule | json | yes |
| phone_reservations | string(20) | no |
| phone_events | string(20) | no |
| email | string(255) | no |
| map_embed_url | text | no |
| pedestrian_access | text | no |
| vehicle_access | text | no |
| amenities | json | no |
| about_content | text | no |

#### Scenario: Schedule JSON structure

- GIVEN schedule JSON: `[{"days":"Dom–Mié","hours":"7am–10pm"},{"days":"Jue","hours":"7am–12am"}]`
- WHEN rendered on contact page
- THEN each entry displays as "Domingo a Miércoles – 7 am a 10 pm"

### Requirement: HomeRestaurantFeature Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| restaurant_id | FK | yes |
| sort_order | int | yes |
| is_active | boolean | yes |

### Requirement: CtaBlock Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| type | enum(evento,renta,contacto) | yes |
| title | string(255) | yes |
| highlighted_word | string(100) | no |
| description | text | no |
| link_url | string(500) | no |
| link_text | string(100) | no |
| is_active | boolean | yes |

### Requirement: SiteSetting Singleton

| Field | Type | Required |
|-------|------|----------|
| site_name | string(255) | yes |
| slogan | string(255) | no |
| logo | media | no |
| favicon | media | no |
| whatsapp_url | string(500) | no |
| instagram_url | string(500) | no |
| facebook_url | string(500) | no |
| tiktok_url | string(500) | no |
| seo_title | string(255) | no |
| seo_description | text | no |
| og_image | media | no |

### Requirement: NewsletterSubscriber Entity

| Field | Type | Required |
|-------|------|----------|
| id | bigint | auto |
| name | string(255) | yes |
| email | string(255) | yes, unique |
| subscribed_at | timestamp | auto |

#### Scenario: Newsletter signup

- GIVEN visitor submits name and email on `/eventos/`
- WHEN form is valid
- THEN NewsletterSubscriber record is created
- AND success message is displayed

### Requirement: Slug Uniqueness

All slug-based entities MUST enforce unique slugs at database level.

#### Scenario: Duplicate slug rejected

- GIVEN restaurant with slug `ahumare` exists
- WHEN admin tries to create another with slug `ahumare`
- THEN validation error prevents save
