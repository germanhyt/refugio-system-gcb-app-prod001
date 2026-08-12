# Content Specification

## Purpose

ServiceItem, EventOffer, HeroSlide video, contact blocks, footer stubs.

## Requirements

### Requirement: ServiceItem Entity

MUST store title, icon/media, sort_order, is_active. Seed MUST cover SDD service list.

#### Scenario: Seeded services

- GIVEN seeders ran WHEN `/servicios` THEN active items in sort order

### Requirement: EventOffer Entity

MUST persist four seedable offers (title; optional desc/image; sort_order; is_active).

#### Scenario: Four offers

- GIVEN seeders + all active WHEN `/eventos` THEN four SDD-titled cards

### Requirement: HeroSlide Media Fields

MUST support media_type image|video, optional video, image for slide/fallback.

#### Scenario: Video failure

- GIVEN unusable video WHEN hero renders THEN UI SHALL NOT break

### Requirement: Home Contact Blocks

Home MUST show 4 blocks (publicidad, comerciales, servicio al cliente, trabaja con nosotros) with SDD contacts.

#### Scenario: Contact section

- GIVEN blocks seeded WHEN `/` THEN 4 labeled blocks appear

### Requirement: Footer Placeholder Pages

Col-2 stubs (Descuentos U. Lima, FAQ, Pet Friendly, estacionamiento) MUST resolve. Blog MUST stay reachable.

#### Scenario: Placeholder

- GIVEN FAQ link WHEN followed THEN HTTP 200

#### Scenario: Blog

- GIVEN Blog link WHEN followed THEN blog routes succeed
