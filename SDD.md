

Cambiaremos la estructura de la web:
1) Opciones del header: Nosotros / Resturantes / Eventos, y la parte derecha de "reserva aqui" permanece
2) En página Nosotros: 
2.a) Hero de "¿Quienes Somos?" 
2.b) Sección de "¡Hola! Somos Refugio Gastronómico." en la Izquierda y en la derecha el texto "TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.
Refugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.
"
2.c) Componente de sección de "
¿Nos visitas?
Nos encuentras en Av. Javier Prado Este 4492
Horarios de atención: ¡Abrimos todos los días!
Domingo a Miércoles: Hasta las 10 p.m.
Jueves: Hasta la 1:00 p.m.
Viernes y sábado: Hasta las 3:00 p.m.
🎶  Música en vivo de jueves a viernes, desde las 8:00 p.m. 
" a la izquierda y de fondo el mapa

3) página de Resturantes
3.a) Hero con "¿Qué te provoca hoy?"
3.b) Mantenemos la grilla de resturantes según categoría
3.c) Componente de sección de "¿Nos visitas?"

4) Página de Eventos:
4.a) Hero con "¡SOMOS EL REFUGIO DE TU DIVERSIÓN!"
4.b) Cards con "Show Musicales" / "Show para niños" / "Organiza tu evento con nosotros" / "Organiza tu fiesta infantil"; ya no va la estructura actual con las fechas que estaban
4.c) Componente de sección de "¿Nos visitas?"

5) Página de Servicios:
5.a) Hero de "Nuestros servicios"
5.b) Grilla de servicios con icons al respecto arriba y texto debajo con "Estacionamiento gratis
Servicios Higiénicos 
Baños para niños
Bosque Mágico: Zona infantil
Delivery
Reservas
Pet friendly 
WhastApp Refugio
Shows en vivo
Shows infantiles
Espacios para eventos
Objetos perdidos
ALQUIER DE ESPACIOS PUBLICITARIOS
ALQUIER DE ZONAS PARA EVENTOS
CATERING
LACTANCIA  
KIT DE EMERGENCIA
"
5.C) Componente de sección de "¿Nos visitas?"


6) La página principal
6.a) Como hero banner permitir cargar video (en caso sea más de un video que recién aparezca los buttons circulares de los lados)
6.b) Sección de "¡Hola! Somos Refugio Gastronómico." ...
6.c) Sección de carousel de "Nuestros resturantes" donde estarán pasando los logos de los resturantes (sea configurable también)
6.d) Sección de "Conoce nuestros servicios" y listar algunos servicio de su página correspondiente y un button debajo de "Ver más"
6.e) Componente de sección de "¿Nos visitas?"
6.f) Sección de "¿Dudas? ¡Contáctanos!" con opciones de 
- "Espacios publicitarios
mike@gcb.pe 
leilah@gcb.pe

994 848 723"
- "Espacios comerciales
mike@gcb.pe 
leilah@gcb.pe

994 848 723"
- "Servicio al cliente
Reservas 

991 318 720"
- "¡Trabaja con nosotros!
991 318 720"
6.g) No se mostrará el blog


7) En el footer
- En una columna las mismas opciones del header
- en una columna otra list de "blog" / "Descuentos U. Lima" / "Preguntas Frecuentes" / "Reglamento de Pet Friednly" / "Política de estacionamiento"
- otra columna de "Términos y condiciones" / "Políticas de privacidad" / "Libro de reclamaciones" / Iocns de redes sociales



8)

8.a) En el detalle de cada resturante consideramos
- Sección con: Nombre si es necesario / Descripción Corta (Escrapear si es necesario y si no hay info, dejamos vacío pero sí o sí es configurable) / Delivery Disponible: Rappi y Peya (logos cada uno scrapear de la web las imgs), esto con url de redirección (de igual forma es configurable por restaurante y puede ser habilitado estos) / Una imagen respecto a su ubicación específica (img no mapa) que se cargará también (opcional)
- Sección de "Ofertas similares"  donde se listen los resturantes de la misma categoría que se consideró en la lógica de grilla de resturantes de su página



8.b)
En la estructura de la página principal
- en "¡Hola! Somos Refugio Gastronómico" consideramos agregar el logo de Refugio,
- en "Nuestros restaurantes" cosnideramos ramas en cascada como se usa en la web principal de https://refugiogastronomico.pe/,
- en "Conoce nuestros servicios" cada servicio en un card rectagular con el icon y el nombre del servicio además que haya 8 servicios listados,
- en "¿Dudas? ¡Contáctanos!" un mejor sin el subrayado que se observa por cada contacto pero si que se sienta que se pueda presionar,

en el detalle de cada resturante
- falta que escrappes logos de rappi y peya configurables por cada restraunte, y su redirección a cada uno de estos, mejoramos la UI de esta sección


---------------------------------------------------------------

11/08/2026
Experto en diseño web y UX/UI

() En el Hero Banner un poco más sombreado degradado en la parte superior as

() En la grilla de restaurantes, cambiamos el estilo a uno con hover, primero mostrar el logo de la marca de forma inicial en la grilla  y al hacer hover que muestre la siguiente imagen (que es una de comida por cada caso). Considerar su parte administrable de este cambios

() En el detalle de cada resturantes
- revisando veo que falta los logos de rappi/peya (colocarlos sí o sí) y su redirección de a su link (opcional), estos 2 logos lo podemos colocar en la parte derecha de la descripción

- El carousel de "Ofertas similares" sea lineal y que haya buttons a los lados (en caso pase el ancho máximo), refactorizar 

() Para la página de nuestro servicios, para cada item puedes considerar un rectagulo o tipo un card verfical donde este el icon y el texto debajo, en la página de inicial mostramos 8 de forma inicial y al dar ver más nos redirige a su página

() En el componente "¿Dudas? ¡Contáctanos!" también la lista de opciones coniderar icons de ser necesario y no subrayado (en su lugar hover)

() En eventos sí lo veo bien pero la idea es que los buttons del card estén en la misma altura (y si es mejor sería mejor), además aplicamos efectos como el hover 

() EN la página de nosotros debajo de "¡Hola! Somos Refugio Gastronómico." consideramos poder agregar imágenes respecto al estanlecimiento, mediante un componente de carousel de imágenes

() Refactorizamos la página de contacto, dado que lo veo desordenado el contenido



() En "Conoce nuestros servicios" en la web principal que se muestre los 8 primeros
() Aún no los logos de rappi y peya en la parte derecha o si está?, además para todos que tengan habilitada la opción de ambos por defecto (no necesariamente está en link)
() En el detalle de resturantes quitamos "Pide ahora" (los 2 componentes) y en link "Ver google maps" 
() En la página de Nosotros, en caso haya considerado la sección del carousel de imágenes, colocamos unas imágenes de prueba para ver cómo queda el caoursel


() En "Nosotros" el carousel de imágenes lo alineamos y consideramos espaciados de forma correcta
() Los logos lo acomodamos en la parte derecha como dos opciones circulares alineadas, y con efecto de hover cada una
() Consideramos mock data en las siguientes opciones "Descuentos U. Lima,
Preguntas Frecuentes,
Reglamento de Pet Friendly,
Política de estacionamiento" con mejora de UI


() Los logos de rappi/peya a la derecha pero en la siguiente columna como tál, y estos logos alineados uno al lado del otro para que se note bien, agranda estos y acomódalos bien para que se note de cada al público
() En contacto colocar una imagen de fondo en el hero banner 
() En el hero banner de la web principal un poco menos de grandiente negro en el contorno 


() En Nosotros y "nuestro espacio" de forma incial consideremos 4 items en el carousel y hacerlo responsive
() Consideramos estas imágenes de fondo para las secciones dónde quedaría por UI correcetas /bg-pajaros-rojo-verde-1 y bg-pajaros-rojo-verde-1 y bg-pagajaras-negro-1


() En el detalle de restaurante, en la columna donde están los componentes de rappi/peya quitamos el contorno y el título que mencione "Pide ahora”,  y si es necesario escarpea en web los logos opcionales como imagen si es necesario y rellenen los circulos estos


() Genial las imágenes de rappi/peya, ahora encima colocaPide ahora" y una flecha curva que los señale, además centro estos logos
() Las imágenes de aves donde la hayas colocado consideramos un fondo sólido también dadoque estas imágenes como tal no tiene fondo


() En el banner de /contacto colocamos un imágenes de fondo sea sóldo a corde a la paleta y decoraciones
() en la página principal entre secciones reutilizamos decoraciones para colocaar en la esquina una rama por ejemplo


() de la página principal la rama de la izquierda lo posicionamos mejor para que nos sea invasiva
() En header fijo que aparece al scrollear que aparezca luego del hero-baner acorde a cada página del proyecto
() recupera el favicon de https://refugiogastronomico.pe y lo colocamos


() de la página principal la rama de la izquierda lo posicionamos poco más la derecha para que se note 
() Como primeros slides colocamos los videos (video1 y video2) sin texto en hero banner principal, y quitamso el test
() En alguna de la secciones usamos el tipo de decoraciones que ves en la imágen


(x) En /contacto que no redunde "Escríbemos"
() De la web de original de Refugio descargarmos y colocamos la decoración siguiente como se ve en la imagen son ramas en casaca de coloca marrón https://refugiogastronomico.pe/ para luego esto colocarlo en la sección de "Nuestros servicios" en el límite superior de la grilla

() imágen para Banner para /blog
() que al aparecer el header al scrollear que no sea tosco además que aparezca el logo a la misma vez que las opciones
() quitamos el "class="rg-leaf-divider rg-leaf-divider--grid-top" de Servicios, y de la imagen como tal obtenemos la decoración señalada para colocarm en el ancho de "Nuestro servicios" 


(x) en header que aparece luego del scroll, consideramos uno 4px espaciado de py en vertical en "rg-sticky-right"
(x) en /contacto no redundar el titulo Escríbenos, el del formulario lo cambiamos
(x) en "Nuestro espacio" los buttons prev y next lo colocamos encima del contendedor de slider a a los lados, y no se vea como que hay un padding  los lados de este


(x) para el header luego de scrollear guiate de la imagen que te comparto (UI), y considera usar logo-v1-base y logo-v2-white para este caso
(x) En nuestros servicios colocamos en la parte iferior izquierda y iferior derecha el decorator-pajaros-rojos y el decorator-platana-inferior-izquierda



De la web
(x) En las sección donde están la grulla de nuestros sevicioes colocamos decoaciones de plantas en la parte superior 
Del panel
(x) quitamos el módulo de newsletters
(x) En "Redes y WhatsApp" inidicamos como texto de descripción en donde están estos links, por ejemplo para el tema de whatssap (estaba en el button del header que dice Reserva aqui)
(x) Luego revisamos si hay consistencia entre la web y el panel punto por punto, o hay algún punto que en el panel que no se está cosniderando o falta revisar



(x) al transicionar al scrollear el header y llega al tope del hero, aparece por un segundo el logo al medio (que no se muestre)
(x) en la página principal quitamos las plantas en "Nuestros restaurantes" y "¡Hola! Somos Refugio Gastronómico.", en su lugar usamos la cinta "divisor-hojas-home" con orientación hacia abajo en la parte superior de la sección de "¡Hola! Somos Refugio Gastronómico."



(x) Dónde está configurable el carousel de imágenes de la página de "Nosotros"? está o no funcional?
(x) los icons por defecto de de la grilla mejorar, ejemplo el icon de whattssap que no se veo bien




- imgs
https://www.dropbox.com/scl/fo/k8y7gy08h01agnd88bfea/ALYBxlV1cYIOAmY6wurWFv8?rlkey=95w9y72nmxjykoeqee65cg7iv&st=3pnw98em&e=1&dl=0
- tabla de info
https://docs.google.com/spreadsheets/d/1-J-MTxzpSYrmb9nn6rju2ySrTpdB5ac7rGAy-8D89mM/edit?gid=0#gid=0

