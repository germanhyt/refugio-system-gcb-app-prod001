# Admin Specification

## Purpose

Filament for restructure. Event CRUD MUST NOT drive `/eventos`.

## Requirements

### Requirement: Hero Slide Video

HeroSlide MUST support image|video. Video MUST muted-autoplay with image fallback.

#### Scenario: Video slide

- GIVEN admin saves video HeroSlide WHEN homepage uses it THEN slide is available

#### Scenario: Image slide

- GIVEN image type WHEN home renders THEN background image works as before

### Requirement: ServiceItem CRUD

Filament MUST CRUD ServiceItem (title, icon/media, sort_order, is_active).

#### Scenario: Activate

- GIVEN inactive item WHEN activated THEN on `/servicios` (MAY on home)

### Requirement: EventOffer CRUD

Filament MUST CRUD EventOffer as `/eventos` source.

#### Scenario: Edit offer

- GIVEN EventOffer WHEN admin updates THEN `/eventos` reflects change

### Requirement: Event Decoupled

Event admin MAY keep legacy slugs; MUST NOT feed offers.

#### Scenario: New Event

- GIVEN admin creates Event WHEN `/eventos` loads THEN EventOffers stay primary

### Requirement: Logo Carousel Config

HomeRestaurantFeature SHOULD control home logos + order.

#### Scenario: Featured logos

- GIVEN featured subset WHEN `/` THEN only those logos in order
