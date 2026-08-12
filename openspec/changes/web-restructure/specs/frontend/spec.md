# Frontend Specification

## Purpose

Public IA per `SDD.md`. Supersedes `refugio-platform` nav/home/events listing.

## Requirements

### Requirement: Header Navigation

Header MUST show Nosotros / Restaurantes / Eventos + «Reserva aquí». Servicios MUST NOT appear. Mobile SHALL mirror.

#### Scenario: Header SDD

- GIVEN any public page WHEN it loads THEN three links + Reserva CTA; no Servicios

### Requirement: Three-Column Footer

Footer MUST have: (1) header nav; (2) Blog / Descuentos U. Lima / FAQ / Pet Friendly / estacionamiento; (3) legal + social.

#### Scenario: Footer columns

- GIVEN any public page WHEN footer renders THEN 3 columns + social work

### Requirement: Homepage Composition

Home MUST order: video hero; Hola; logo carousel; services preview + «Ver más»; visit-us; 4 contact blocks. MUST NOT show blog/IG.

#### Scenario: Home sections

- GIVEN active slides/services WHEN `/` loads THEN six SDD sections; no blog/IG

#### Scenario: Hero controls

- GIVEN 1 slide WHEN `/` THEN prev/next hidden; GIVEN 2+ THEN shown

### Requirement: Inner Pages

| Page | MUST include |
|------|----------------|
| `/nosotros` | «¿Quiénes Somos?», Hola split, visit-us |
| `/restaurantes` | «¿Qué te provoca hoy?», category grid, visit-us |
| `/eventos` | SDD hero, 4 EventOffers (not Event dates), visit-us |
| `/servicios` | «Nuestros servicios», ServiceItem grid, visit-us |

`/eventos/{slug}` MAY resolve; MUST NOT link from primary listing.

#### Scenario: Eventos offers

- GIVEN 4 active EventOffers WHEN `/eventos` THEN 4 cards + visit-us; no date list

#### Scenario: Legacy slug

- GIVEN Event slug WHEN opened directly THEN detail succeeds

### Requirement: Shared Visit-Us

Visit-us (VisitInfo) MUST appear on Home, Nosotros, Restaurantes, Eventos, Servicios.

#### Scenario: Five surfaces

- GIVEN VisitInfo set WHEN each loads THEN visit-us matches on all five
