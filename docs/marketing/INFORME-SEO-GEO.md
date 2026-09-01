# Informe de SEO y GEO aplicado a refugiogastronomico.pe

**Proyecto:** Refugio Gastronómico — `https://refugiogastronomico.pe`
**Audiencia:** Equipo de Marketing
**Fecha:** Septiembre 2026
**Estado:** Fase 1 implementada y desplegada en producción

---

## 1. Resumen ejecutivo

Se aplicó al sitio web de Refugio Gastronómico una capa de **SEO técnico** y **GEO (Generative Engine Optimization)** para que la marca y sus restaurantes sean **reconocidos, citados y recomendados** tanto por los buscadores tradicionales (Google) como por los **motores de IA generativa** (ChatGPT, Perplexity, Google AI Overviews, Gemini, Claude).

La Fase 1 (la que ya está en producción) cubre los cimientos técnicos: datos estructurados, sitemap, redirecciones que conservan el valor SEO acumulado del sitio anterior, y normalización de URLs. Con esto, el sitio está **indexable, citable por IA y sin pérdida de autoridad** respecto al WordPress previo.

La Fase 2 (pendiente) es la que depende de Marketing: contenido, Google Business Profile, menciones externas y medición.

---

## 2. Qué es GEO y por qué le importa a Marketing

El **GEO** (también llamado *LLMO — Large Language Model Optimization*) es la disciplina que busca que una marca aparezca en las **respuestas sintetizadas** que dan las IA cuando un usuario pregunta cosas como:

- *"¿Qué restaurantes visitar en Surco, Lima?"*
- *"¿Un parque gastronómico pet friendly en Lima?"*
- *"¿Dónde hay música en vivo y buena comida en Javier Prado?"*

A diferencia del SEO clásico (donde competimos por un **link azul** en una lista de resultados), en GEO competimos por ser **mencionados dentro de un párrafo** que la IA construye como respuesta. Las IA no "clican"; **leen y citan**. Para citarnos necesitan:

1. **Entendernos** (datos estructurados: qué somos, dónde, horarios, cocina, teléfono).
2. **Encontrarnos** (sitemap, URLs limpias, indexación).
3. **Confian en nosotros** (autoridad, menciones, Google Business Profile, NAP consistente).
4. **Tener contenido citable** (textos claros, FAQs, descripciones de restaurantes).

La Fase 1 cubre **1 y 2**. La Fase 2 cubre **3 y 4**.

---

## 3. Estado anterior vs. estado actual

| Aspecto | Antes (WordPress) | Ahora (Laravel + GEO) |
|---|---|---|
| Datos para máquinas (Schema.org) | No estructurados | JSON-LD en todas las páginas clave |
| Sitemap | Sí, pero del WP | Sitemap nuevo con 100 URLs, en el dominio correcto |
| URLs | Con barra final y slugs distintos | Normalizadas y redirigidas (301) a las nuevas |
| Dominio canónico | Mezclaba www/apex | www → apex (una sola URL canónica) |
| HTTPS | Sí | Sí (Let's Encrypt, autorenovación) |
| Archivo para IA | No existía | `llms.txt` (descripción en texto plano para IA) |
| Redirecciones de páginas viejas | 404 (pérdida de SEO) | 301 (conserva la autoridad) |

**Lo más importante para Marketing:** las URLs viejas indexadas en Google (restaurantes con slugs antiguos, páginas como `/contacto/`, `/convocatorias/`, etiquetas, eventos pasados) **ya no se pierden en 404**; ahora **redirigen 301** a su equivalente nuevo, conservando todo el valor SEO acumulado durante años.

---

## 4. Lo que se implementó (Fase 1)

### 4.1 Datos estructurados (JSON-LD) — el "idioma" de las IA
Se inyectó en cada página un bloque de datos que las IA y Google leen directamente:

- **Página de inicio:** el parque gastronómico como `FoodEstablishment` (nombre, dirección, geo, horarios, teléfono, amenidades: pet friendly, estacionamiento, música en vivo, zona infantil, redes sociales).
- **Cada restaurante:** `FoodEstablishment` con nombre, cocina que sirve, link al menú, teléfono de reservas, redes, imagen y relación con el parque (organización padre).
- **Migas de pan (BreadcrumbList):** en cada restaurante y evento, para que la IA entienda la jerarquía Inicio › Restaurantes › [Nombre].
- **FAQ (FAQPage):** en la página de Preguntas Frecuentes, para que las IA citen nuestras respuestas textuales.
- **Eventos:** `Event` con fecha, hora, ubicación (el parque) y descripción.

### 4.2 SEO on-page
- **Canonical** en todas las páginas (una sola URL canónica por contenido).
- **Twitter Cards** y **Open Graph** (previsualización bonita al compartir en redes).
- **Meta description** y **title** por restaurante y evento (campos editables desde el CMS).

### 4.3 Discoverabilidad
- **Sitemap** con 100 URLs en `https://refugiogastronomico.pe/sitemap.xml`.
- **robots.txt** actualizado apuntando al sitemap y permitiendo a los crawlers de IA.
- **llms.txt** en `https://refugiogastronomico.pe/llms.txt`: un resumen en texto plano pensado para que las IA entiendan rápidamente qué es Refugio, sus conceptos y enlaces clave.

### 4.4 Redirecciones 301 (conservación de autoridad)
Se mapearon las URLs viejas del WordPress a las nuevas:

- **Slugs de restaurante que cambiaron** (8): ej. `curich` → `cremoladas-curich`, `bros` → `broaster-bros`, `puerto-mancora` → `barrio-mancora`, etc.
- **Restaurantes retirados** (2): `la-choza-de-la-anaconda`, `caja-china-criolla` → listado de restaurantes.
- **Páginas fusionadas:** `/contacto` → inicio, `/convocatorias` → `/convocatoria`, `/gracias-contacto` → inicio, `/plantilla` → inicio.
- **Etiquetas y eventos pasados** del WP → índices correspondientes.
- **Normalización de barra final:** `/nosotros/` → `/nosotros` (todas las URLs viejas del WP traían `/`).

### 4.5 Dominio canónico y HTTPS
- `www.refugiogastronomico.pe` → **301** → `refugiogastronomico.pe` (una sola versión del sitio).
- HTTPS con certificado Let's Encrypt, **renovación automática**.

---

## 5. Cómo debería verse Refugio en los motores de IA

Con la Fase 1, cuando alguien pregunte a ChatGPT/Perplexity/Gemini por un parque gastronómico en Surco con +20 restaurantes, pet friendly y música en vivo, la IA tiene ahora **todo lo necesario para citarnos**:

- Sabe que somos un `FoodEstablishment` en Av. Javier Prado Este 4492, Santiago de Surco.
- Sabe nuestros horarios y amenidades.
- Sabe la lista de restaurantes y su cocina.
- Tiene un sitemap y `llms.txt` para descubrirnos y citarnos.

**Lo que falta (Fase 2) para que nos recomiende con seguridad:** contenido editorial propio (blog activo), Google Business Profile optimizado y menciones en sitios externos (TripAdvisor, Google Maps, reseñas). Eso es trabajo de Marketing + contenido.

---

## 6. KPIs y cómo medirlos

| KPI | Qué mide | Herramienta |
|---|---|---|
| Indexación | Cuántas URLs están en Google | Google Search Console (cobertura) |
| Aparición en AI Overviews | Si aparecemos en respuestas de Google AI | Buscar queries de marca en Google |
| Citas en IA | Si ChatGPT/Perplexity nos mencionan | Preguntar manualmente / herramientas de GEO tracking |
| CTR y posiciones | Tráfico orgánico | Search Console (rendimiento) |
| Redirecciones 301 | Que no haya 404 heredados | Search Console + logs del server |
| Backlinks y menciones | Autoridad externa | Google Business Profile, TripAdvisor, reseñas |

**Acción inmediata de Marketing:** registrar y verificar el sitio en **Google Search Console** (si no lo está) para medir la indexación del nuevo dominio tras el cutover.

---

## 7. Próximos pasos (Fase 2 — dependen de Marketing)

1. **Google Business Profile**: optimizar la ficha del parque (dirección, horarios, fotos, atributos pet friendly/estacionamiento, productos/servicios). Es la señal local más fuerte para IA.
2. **NAP consistente**: que Nombre, Dirección y Teléfono sean **idénticos** en Google, TripAdvisor, Facebook, Instagram y la web.
3. **Contenido editorial (blog)**: reactivar el blog con artículos sobre los conceptos, eventos y la experiencia (las IA citan texto original y bien escrito).
4. **FAQ ampliada**: enriquecer la página de Preguntas Frecuentes con las dudas reales que la gente busca (las que ChatGPT responde hoy sin citarnos).
5. **Menciones externas**: conseguir aparición en listados tipo "mejores parques gastronómicos de Lima", reseñas en TripAdvisor, artículos de prensa.
6. **Medición de GEO**: empezar a registrar periódicamente qué responden las IA a las queries objetivo, para ver si empezamos a ser citados.

---

## 8. Anexo técnico (para el equipo web)

### Rutas y archivos clave
- Servicio SEO: `app/Services/SeoService.php`
- Layout con inyección: `resources/views/layouts/app.blade.php`
- Redirecciones 301: `routes/web.php`
- Middleware barras: `app/Http/Middleware/StripTrailingSlash.php`
- Comando sitemap: `php artisan refugio:sitemap` (genera `public/sitemap.xml`)
- Archivo para IA: `public/llms.txt`
- robots: `public/robots.txt`

### URLs verificables en producción
- `https://refugiogastronomico.pe/sitemap.xml`
- `https://refugiogastronomico.pe/llms.txt`
- `https://refugiogastronomico.pe/robots.txt`
- Validador de datos estructurados: https://search.google.com/test/rich-results (pegar una URL de restaurante o la home)

### Queries objetivo sugeridas para medir GEO
- "parque gastronómico en Lima"
- "restaurantes pet friendly en Surco"
- "música en vivo y comida en Javier Prado"
- "dónde comer con familia en Santiago de Surco"
- "Refugio Gastronómico horarios"

---

## 9. Conclusión

La Fase 1 deja al sitio **técnicamente listo** para ser indexado y citado por IA y buscadores, sin perder la autoridad acumulada del WordPress. A partir de aquí, **el impacto real en GEO dependerá del contenido y la presencia externa** (Google Business Profile, blog, menciones), que es la Fase 2 y requiere impulsión de Marketing.

**Recomendación:** arrancar la Fase 2 priorizando **Google Business Profile** y **NAP consistente** (mayor retorno, menor esfuerzo) y luego **contenido editorial**.
