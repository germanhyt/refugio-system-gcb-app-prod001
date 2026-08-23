# Exploration: Estrategia GEO para Refugio Gastronómico

> Propuesta profesional de Generative Engine Optimization (GEO) basada en investigación de mejores prácticas 2026 y auditoría del código del proyecto.

---

## 1. Contexto del Negocio

**Refugio Gastronómico** es un **food park de ubicación única** en Santiago de Surco, Lima (Av. Javier Prado Este 4492, coords `-12.0842658, -76.9734978`) que agrupa ~21 conceptos gastronómicos bajo un mismo techo. Marca: "Juntos todo sabe mejor". Plataforma: Laravel 11 + Filament 3 (`refugiogastronomico.pe`), clon funcional del sitio WordPress/Elementor original, con scraper de importación.

Su modelo —"destino gastronómico" más que restaurante individual— lo posiciona de forma única frente a la búsqueda generativa, pero **hoy es prácticamente invisible para los motores de IA**.

### Conceptos (21 tenants)
Cavenecia (parrilla Angus), Barrio Mancora (ceviche/criolla), Sisa (café/desayunos), Refugio Bar, Don Melchor (pollo a la brasa), Ahumare, Anticuching, Madre Amazónica (selva/criolla/mar), La 22 (burgers/salchipapas), La Victoria (sanguches criollos), Tortas Gaby (postres), Barrio Wok (chifa), Lili Blue (peruana), Saltao (saltados), Broaster Bros, Ramen Ya!, Hanzo Express (nikkei street food), Mr Smash (smash burger), Caldos Doris, Limanesas (milanesas), Nashmy (árabe), Curich (cremoladas desde 1942).

### Contacto / redes (nivel parque)
- WhatsApp reservas: `https://wa.link/ltbwxk` · Tel reservas: 991 318 720 · Eventos: 994 848 723
- Instagram, Facebook (RefugioParqueGastronomico), TikTok (@refugiogastronomico.pe)
- Emails: `hola@refugiogastronomico.pe`, `marketing@refugiogastronomico.pe` (inconsistencia: seeders usan `leilah@gcb.pe`, `mike@gcb.pe`)

---

## 2. ¿Qué es GEO y por qué importa para Refugio?

| | SEO tradicional | GEO |
|---|---|---|
| **Salida** | Lista de links clicables | Respuesta narrativa sintetizada |
| **Comportamiento** | El usuario hace clic | El usuario obtiene la respuesta directa |
| **Consulta** | ~4 palabras | ~23 palabras, conversacional |
| **Métrica de éxito** | Rankings, CTR, tráfico | Citaciones, menciones, share of voice |
| **Foco** | Palabras clave + backlinks | Estructura de contenido + señales de entidad |
| **Pregunta clave** | "¿Estamos en la página 1?" | "¿Estamos dentro de la respuesta?" |

Los motores generativos **no inventan; sintetizan** a partir de: Google Business Profile, contenido del sitio, plataformas de reseñas (Google, Yelp, TripAdvisor), blogs de comida, directorios locales y menciones en redes. GEO consiste en asegurar que **todas esas fuentes den a la IA material preciso, estructurado y citable**.

**Ventaja competitiva de Refugio:** el modelo "food park con 21 conceptos" responde naturalmente a consultas de alto valor que un restaurante único no puede cubrir:

- "¿dónde comer en grupo en Surco con opciones variadas?"
- "food park con música en vivo en Lima"
- "sitio pet-friendly con opciones para toda la familia en Surco"
- "lugar con ceviche, parrilla y ramen en el mismo sitio"

Ningún competidor individual cubre ese espectro. GEO debe **capturar esas consultas conversacionales largas**, terreno natural de la IA.

---

## 3. Auditoría del Estado Actual

### 3.1 Lo que ya funciona (base sólida)

| Activo | Estado | Valor GEO |
|---|---|---|
| `<title>` y `<meta description>` por página | Implementado | Medio |
| Open Graph completo | Implementado | Bajo (social, no IA) |
| Meta editable por restaurante | Implementado | Medio |
| SEO global editable en admin (Filament) | Implementado | Alto |
| `robots.txt` permite todos los crawlers | `User-agent: * / Disallow:` | **Crítico — ya correcto** |
| Dirección única + coordenadas en BD (`VisitInfo`) | Implementado | Alto — falta exponer en schema |
| FAQ factual (horarios, parqueo, mascotas, eventos) | Contenido existe | Alto — falta marcar |
| Contenido estático rico (pet-friendly, parqueo, descuentos U. Lima) | `config/static-pages.php` | Alto |
| Descripciones por concepto + categorías de cocina | En seeder/BD | Medio — falta enlazar entidad |
| Redes sociales (IG, FB, TikTok) | En BD | Alto — para `sameAs` |

### 3.2 Brechas críticas para GEO

| Brecha | Impacto | Esfuerzo |
|---|---|---|
| **Sin JSON-LD / Schema.org** en el frontend | Crítico — la IA no puede extraer hechos | Medio |
| **Sin sitemap XML** generado por Laravel | Alto — gap de descubribilidad | Bajo |
| **Sin URLs canónicas** | Medio — ambigüedad de entidad | Bajo |
| **Sin Twitter Card tags** | Bajo | Bajo |
| **`/contacto` devuelve 404** mientras CTAs apuntan ahí | Medio — embudo roto | Bajo |
| **Sin `llms.txt`** | Bajo-experimental (Google no lo usa; otros motores sí) | Bajo |
| **FAQ sin `FAQPage` schema** | Alto — contenido Q&A no citable | Bajo |
| **Sin Google Business Profile API** ni gestión activa | Crítico — señal local más pesada | Medio (proceso) |
| **NAP inconsistente** en emails | Medio — erosiona confianza de la IA | Bajo |
| **Sin atributos dietéticos/ocasión** (vegano, sin gluten, familiar, romántico) | Alto — la IA filtra por estos | Medio |
| **Sin reseñas estructuradas** en sitio | Medio | Medio |

> **Hallazgo clave:** el `robots.txt` ya permite crawlers de IA, por lo que **no estamos bloqueados** — el problema es de **contenido estructurado y señales de entidad**, no de acceso. Eso sitúa el apalancamiento en schema + contenido, no en infraestructura de crawling.

---

## 4. Investigación 2026 (Hallazgos Clave)

- **GEO vs SEO:** ~50–60% del trabajo se solapa. Lo único de GEO: estructuración para extracción por IA, optimización de entidad sobre densidad de keywords, y configuración de acceso para crawlers de IA.
- **Google (mayo 2026):** primera guía oficial — **no hay schema obligatorio** para AI Overviews/AI Mode; **`llms.txt` no afecta** la visibilidad en Google. Schema **no garantiza citación**.
- **Rol indirecto real de schema:** elimina ambigüedad para sistemas que construyen grafos de entidad (Knowledge Graph). Resuelve "¿es fruta o empresa?". No es palanca directa de ranking/citación.
- **Práctica recomendada:** implementar schema que **refleje la estructura real del contenido** (Article, Organization, FoodEstablishment con atributos precisos, Person para autores). **No** añadir `FAQPage` a páginas sin FAQ real solo por citabilidad (ya ni da rich snippet en Google clásico).
- **`llms.txt`:** opcional/experimental. Estudio independiente (SE Ranking, 300k dominios) confirma insignificancia estadística vs. citación. Útil para **agentes y asistentes de código** que leen documentación del sitio programáticamente. Bajo esfuerzo, alta señal para algunos motores no-Google.
- **Campos schema críticos que la mayoría omite** (y que para Refugio son oro): `priceRange`, `amenityFeature`, `hasMap`, `servesCuisine`, `openingHoursSpecification`, `areaServed`, `sameAs`, atributos dietéticos/ocasión. La IA usa estos para emparejar negocios con intenciones de consulta. **Campos faltantes = coincidencias perdidas.**
- **GBP:** señal local más pesada. Completar todos los campos, fotos mensuales, publicaciones semanales, responder toda reseña en 24–48 h. Listings con fotos ven ~35% más engagement.
- **NAP:** inconsistencias triviales ("Street" vs "St.") reducen mediblemente la confianza de la IA para citar. Estandarizar formato una vez, en todas partes.
- **Autoridad distribuida:** la IA pesa el acuerdo entre fuentes. Ser el único lugar que afirma algo sobre tu negocio es señal débil. Menciones de terceros (directorios, prensa, Reddit, grupos FB locales, Nextdoor) apalancan más que pulir el homepage en aislamiento.
- **Resultados:** esperables en 60–90 días conforme los motores reindexan los datos estructurados actualizados.

---

## 5. Objetivos Estratégicos

| # | Objetivo | KPI | Meta a 90 días |
|---|---|---|---|
| O1 | Ser citado por nombre en respuestas de IA para food park en Lima/Surco | Frecuencia de citación (ChatGPT, Perplexity, Gemini, AI Overviews) | ≥ 8 de 10 consultas objetivo |
| O2 | Que la IA describa Refugio con hechos correctos | Precisión de citación | ≥ 90% |
| O3 | Superar a competidores en visibilidad generativa | Share of voice ("food park / parque gastronómico Lima") | ≥ 40% |
| O4 | Generar tráfico atribuible desde IA | Visitas desde fuentes de IA (GA4) | Línea base + medible |
| O5 | Posicionar los 21 conceptos como sub-entidades citables | Menciones de conceptos individuales | Top 5 conceptos citables |
| O6 | Resolver embudo roto (`/contacto` 404) y unificar NAP | Consistencia NAP sitio + GBP + directorios | 100% |

---

## 6. Pilares de la Estrategia GEO

### Pilar 1 — Datos Estructurados (Schema.org / JSON-LD)
El puente entre SEO y GEO. Para Google = rich snippets; para IA = hechos citables.

**Entidades a modelar:**

```
Organization / FoodEstablishment (Refugio Gastronómico)
   ├── sameAs → Instagram, Facebook, TikTok, sitio
   ├── address → PostalAddress (Av. Javier Prado Este 4492, Surco, Lima, PE)
   ├── geo → GeoCoordinates (-12.0842658, -76.9734978)
   ├── openingHoursSpecification → horarios del parque
   ├── amenityFeature → pet-friendly, parqueo, música en vivo, Bosque Mágico
   └── subOrganization → [21 FoodEstablishment]

FoodEstablishment (por concepto)
   ├── name, description, servesCuisine, priceRange
   ├── menu → URL del PDF / página
   ├── parentOrganization → Refugio Gastronómico
   ├── sameAs → Instagram/TikTok propios
   └── hasMenu → Menu (hasMenuSection + hasMenuItem)

FAQPage (FAQ maestra + FAQ por concepto)
   └── mainEntity → [Question → Answer]

Event / Service · BreadcrumbList
```

**Patrón `@id`** para enlazar nodos entre páginas (parque ↔ conceptos) y mejorar resolución de entidad en el Knowledge Graph.

### Pilar 2 — Contenido Estructurado para Extracción (AEO)
La IA premia la claridad semántica sobre el pulido de marketing. Responder primero, adornar después.

- **FAQ maestra** (10–15 Q&A, respuestas directas 100–200 palabras con detalles concretos: precios, horarios, coordenadas, credenciales).
- **FAQ por concepto** (top 5–8): "¿cuál es el plato estrella de Cavenecia?", "¿Barrio Wok tiene opción vegetariana?".
- **Páginas "guía de barrio"**: "Dónde comer en Surco", "Food parks en Lima" — contenido de localidad citable.
- **TL;DR bajo cada H2** — frases autosuficientes como respuesta.
- Jerarquía limpia H1→H2→H3; párrafos cortos; respuesta en la primera frase.

### Pilar 3 — Google Business Profile + Reseñas
Para negocios locales, el GBP es una de las señales más pesadas que usa la IA.

- Completar **todos** los campos (categorías, atributos dietéticos, horarios, fotos).
- Publicar en GBP **semanalmente** (ofertas, eventos, novedades de conceptos).
- Responder **toda reseña en 24–48 h**.
- Campaña de reseñas **detalladas y específicas** (la IA necesita el lenguaje con que describir el lugar).
- **NAP consistente** en sitio, GBP, Yelp, TripAdvisor, redes — estandarizar formato una vez.

### Pilar 4 — Autoridad de Entidad Distribuida
La IA pesa el acuerdo entre fuentes. Ser el único lugar que afirma algo es señal débil.

- Outreach a **3–5 publicaciones locales** (blogs de comida Lima, directorios, prensa) para menciones editoriales con detalle.
- Directorios: Yelp, TripAdvisor, Google Maps, PedidosYa/Rappi — asegurar consistencia.
- Menciones en comunidades (Reddit hilos Lima, grupos FB locales, Nextdoor) — terreno del "near me" y voz.
- Enlazar conceptos con sus marcas propias (Cavenecia, Don Melchor, Tortas Gaby tienen sitio propio) para construir **sub-entidades** citables.

### Pilar 5 — Fundamentos Técnicos
- **Sitemap XML** generado por Laravel (comando `GenerateSitemap`, ya planeado en `tasks.md`).
- **URLs canónicas** en todas las páginas.
- **Twitter Card tags**.
- `llms.txt` **opcional/experimental** — bajo esfuerzo, alta señal para algunos motores. Mantener claims y links actualizados; no sustituye a HTML rastreable.
- **Resolver `/contacto` 404** — redirigir o restaurar la ruta.
- Verificar que el contenido clave no esté tras login/paywall/JS interactivo (Blade SSR ya lo cumple).

---

## 7. Plan de Acción por Fases (90 días)

### Fase 1 — Fundaciones Técnicas y Datos Estructurados (Semanas 1–3)

| Semana | Tarea | Entregable |
|---|---|---|
| 1 | Auditoría NAP completa (sitio, GBP, Yelp, TripAdvisor, redes). Test de visibilidad IA para 10 consultas objetivo (línea base) | Reporte de línea base + dashboard de consultas |
| 1 | Implementar `Organization` + `LocalBusiness`/`FoodEstablishment` JSON-LD del parque con `GeoCoordinates`, `OpeningHoursSpecification`, `sameAs`, `amenityFeature`, `priceRange` | Schema del parque en producción |
| 2 | Implementar `FoodEstablishment` por concepto (21) en `restaurantes/{slug}` con `servesCuisine`, `menu`, `parentOrganization`, `sameAs` | Schema por restaurante |
| 2 | Generar sitemap XML (`GenerateSitemap`); añadir URLs canónicas y Twitter Cards | Sitemap + canonicals |
| 3 | Resolver `/contacto` 404 (redirigir o restaurar). Unificar NAP y emails en todo el sitio y CMS | Embudo reparado + NAP consistente |
| 3 | Publicar `llms.txt` experimental (curado, claims y links actualizados) | `llms.txt` en raíz |

### Fase 2 — Contenido Citable y GBP (Semanas 4–7)

| Semana | Tarea | Entregable |
|---|---|---|
| 4 | Construir **FAQ maestra** (10–15 Q&A, respuestas directas 100–200 palabras) + `FAQPage` schema. Reescribir copia "about" en formato answer-first | Página FAQ optimizada + schema |
| 5 | **FAQ por concepto** (top 5–8: Cavenecia, Barrio Wok, Mr Smash, Don Melchor, Ramen Ya, Madre Amazónica, Tortas Gaby) | FAQ por concepto |
| 5 | Completar **todos** los atributos del GBP (dietéticos, ocasión: familiar/romántico/grupos, horarios, fotos). Programar publicaciones semanales | GBP 100% completo |
| 6 | Campaña de solicitud de reseñas detalladas en Google, Yelp, TripAdvisor (con guía de qué mencionar) | +N reseñas específicas |
| 6 | Marcar contenido estático existente (pet-friendly, parqueo, descuentos U. Lima) con schema relevante | Páginas estáticas marcadas |
| 7 | Páginas "guía de barrio": "Dónde comer en Surco", "Food parks en Lima" | 2–3 guías de barrio |

### Fase 3 — Autoridad Distribuida y Medición (Semanas 8–12)

| Semana | Tarea | Entregable |
|---|---|---|
| 8 | Outreach a 3–5 publicaciones/blogs locales de comida en Lima para menciones editoriales con detalle | 3+ menciones editoriales |
| 9 | Asegurar consistencia en directorios (Yelp, TripAdvisor, PedidosYa, Rappi) y enlazar sub-entidades (sitios propios de Cavenecia, Don Melchor, etc.) | Directorios auditados y corregidos |
| 10 | Configurar tracking en GA4 (atribución de tráfico desde fuentes de IA) + dashboard de citación mensual | Dashboard de KPIs |
| 11 | Re-test de las 10 consultas objetivo en ChatGPT, Perplexity, Gemini, AI Overviews. Comparar vs. línea base | Reporte de citación (60 días) |
| 12 | Iteración: reforzar gaps detectados, ampliar FAQ a conceptos secundarios, refinar schema | Plan de mantenimiento |

---

## 8. Implementación Técnica Específica (Schema.org)

**Ubicación sugerida:** un `SeoService` (ya planeado en `tasks.md`) que genere JSON-LD dinámico desde los modelos `SiteSetting`, `VisitInfo`, `Restaurant`, `Event`, y lo inyecte en `layouts/app.blade.php` vía `@stack('json-ld')`.

**Esquema del parque (ejemplo conceptual):**

```json
{
  "@context": "https://schema.org",
  "@type": "FoodEstablishment",
  "@id": "https://refugiogastronomico.pe/#refugio",
  "name": "Refugio Gastronómico",
  "alternateName": "El Refugio",
  "slogan": "Juntos todo sabe mejor",
  "description": "Parque gastronómico con +20 propuestas...",
  "url": "https://refugiogastronomico.pe",
  "servesCuisine": ["Parrilla","Criolla","Nikkei","Ramen","Chifa","Árabe","Ceviche","Café","Postres"],
  "priceRange": "$$",
  "telephone": "+51 991 318 720",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Av. Javier Prado Este 4492",
    "addressLocality": "Santiago de Surco",
    "addressRegion": "Lima",
    "addressCountry": "PE"
  },
  "geo": { "@type": "GeoCoordinates", "latitude": -12.0842658, "longitude": -76.9734978 },
  "hasMap": "https://www.google.com/maps/...",
  "openingHoursSpecification": [ { "@type": "OpeningHoursSpecification", "dayOfWeek": [], "opens": "", "closes": "" } ],
  "amenityFeature": [
    { "@type": "LocationFeatureSpecification", "name": "Pet-friendly", "value": true },
    { "@type": "LocationFeatureSpecification", "name": "Estacionamiento", "value": true },
    { "@type": "LocationFeatureSpecification", "name": "Música en vivo", "value": true },
    { "@type": "LocationFeatureSpecification", "name": "Zona infantil (Bosque Mágico)", "value": true }
  ],
  "sameAs": [
    "https://www.instagram.com/refugiogastronomico.pe/",
    "https://www.facebook.com/RefugioParqueGastronomico",
    "https://www.tiktok.com/@refugiogastronomico.pe"
  ],
  "subOrganization": [
    { "@type": "FoodEstablishment", "name": "Cavenecia", "url": ".../restaurantes/cavenecia", "servesCuisine": "Parrilla" },
    { "@type": "FoodEstablishment", "name": "Barrio Wok", "url": ".../restaurantes/barrio-wok", "servesCuisine": "Chifa" }
  ]
}
```

**Notas de rigor (investigación 2026):**
- Google (mayo 2026): **no hay schema obligatorio** para AI Overviews/AI Mode, y **`llms.txt` no afecta** la visibilidad en Google. Schema **no garantiza citación**.
- Schema retiene un rol **indirecto real**: elimina ambigüedad para los sistemas que construyen grafos de entidad (Knowledge Graph).
- Por tanto: implementar schema que **refleje la estructura real del contenido**, no añadir `FAQPage` a páginas sin FAQ real solo por efecto de citabilidad (ya ni da rich snippet en Google clásico).
- `llms.txt` = opcional/experimental, bajo esfuerzo. Útil para agentes y asistentes de código que leen documentación del sitio programáticamente.

---

## 9. Consultas Objetivo a Optimizar

Línea base para medir citación (testear en ChatGPT, Perplexity, Gemini, AI Overviews):

| # | Consulta conversacional | Intención |
|---|---|---|
| 1 | "¿dónde comer en grupo en Surco con opciones variadas?" | Grupos + variedad |
| 2 | "food park o parque gastronómico en Lima" | Categoría del negocio |
| 3 | "sitio pet-friendly con opciones para toda la familia en Surco" | Amenidades |
| 4 | "lugar con música en vivo y comida en Surco" | Ocasión |
| 5 | "¿qué restaurantes hay en Av. Javier Prado Este, Surco?" | Localidad |
| 6 | "ceviche, parrilla y ramen en el mismo lugar en Lima" | Multi-cocina |
| 7 | "parque gastronómico con estacionamiento en Surco" | Logística |
| 8 | "lugares para evento familiar con comida variada en Lima" | Eventos |
| 9 | "¿qué es Refugio Gastronómico?" | Identidad de entidad |
| 10 | "pollo a la brasa en Surco con parqueo" | Concepto + logística |

---

## 10. KPIs y Medición

| KPI | Herramienta | Frecuencia |
|---|---|---|
| Frecuencia de citación (¿aparece Refugio en la respuesta?) | Tests manuales + herramientas de monitoreo GEO | Mensual |
| Share of voice vs. competidores | Análisis de menciones en IA | Mensual |
| Precisión de citación (hechos correctos) | Revisión manual de respuestas | Mensual |
| Sentimiento de citación (positivo/neutral/negativo) | Revisión manual | Mensual |
| Tráfico atribuido a IA | GA4 (atribución de fuentes) | Semanal |
| Volumen y especificidad de reseñas | GBP, Yelp, TripAdvisor | Mensual |
| Consistencia NAP | Auditoría de directorios | Trimestral |

> **Métrica reina:** "¿estamos dentro de la respuesta?" — no "¿estamos en la página 1?". Resultados esperables en **60–90 días** conforme los motores reindexan los datos estructurados actualizados.

---

## 11. Riesgos y Consideraciones

| Riesgo | Mitigación |
|---|---|
| Schema no garantiza citación (Google mayo 2026) | No tratar schema como palanca directa; combinar con contenido answer-first + GBP + menciones de terceros |
| `llms.txt` insignificante estadísticamente para Google | Mantenerlo solo como infraestructura de bajo costo para agentes; no depender de él |
| `/contacto` 404 erosiona confianza y embudo | Resolver en Semana 3, antes de escalar contenido |
| Inconsistencia de emails (hola@, leilah@gcb.pe, mike@gcb.pe) | Unificar en CMS y todo el sitio en Semana 3 |
| 21 conceptos sin NAP propio (correcto para food hall) | No inventar direcciones por concepto; modelar como `subOrganization` del parque |
| Competencia de reseñas en Lima | Campaña activa de reseñas detalladas + respuestas en 24–48 h |
| Contenido desactualizado penaliza (frescura) | Calendario de actualización: GBP semanal, FAQ/menús trimestral |

---

## 12. Resumen y Próximos Pasos

**En una frase:** convertir el material factual ya existente en el CMS Filament en **infraestructura legible para IA** — schema completo del food park + FAQ answer-first + GBP activo + menciones de terceros — con resultados medibles en 90 días.

**Próximos pasos:**

1. **Aprobar la propuesta** y confirmar el alcance de las 3 fases.
2. **Acceso al Google Business Profile** (gestión activa) — señal local más pesada, requiere proceso.
3. **Iniciar Fase 1** (schema del parque + sitemap + resolver `/contacto`) — apalancamiento más alto, menor riesgo.
4. **Definir responsable de contenido** para FAQ maestra y guías de barrio (Fase 2).
5. **Establecer la línea base** de citación en las 10 consultas objetivo antes de tocar nada.

---

## Fuentes de investigación (2026)

- Search Engine Land — "Mastering generative engine optimization in 2026: Full guide"
- LLM Pulse — "Generative Engine Optimization (GEO): The Complete Guide for 2026"
- LLMrefs — "GEO: The 2026 Guide to AI Search Visibility"
- CiteAI Search — "GEO vs SEO: What's the Difference and Why You Need Both in 2026"
- Egor Mishin — "GEO, AEO and AIO: what actually works in LLM search optimization" (ref. Google guide mayo 2026, SE Ranking study 300k dominios)
- Over The Top SEO — "Local GEO: Optimizing for AI-Generated Local Search Results"
- Enception — "Local GEO for Restaurants: Get Recommended by AI Search"
- Adnnel — "GEO for Restaurants: Get Cited by AI Search in 2026"
- Beancount.io — "GEO: How Small Businesses Get Cited by ChatGPT, Perplexity, and Google AI Overviews"
- llmstxtgenerator.de — "GEO vs. SEO 2026 – What Is the Difference?"
