<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MirroredPageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirecciones 301 — Cutover WordPress → Laravel
| Conservan el valor SEO acumulado en las URLs viejas indexadas.
| Las URLs del WP origen traen barra final; StripTrailingSlash las normaliza,
| luego estas reglas mapean slugs cambiados/retirados/páginas fusionadas.
|--------------------------------------------------------------------------
*/

// Páginas estáticas del WP que cambiaron o se fusionaron
Route::redirect('/gracias-contacto', '/', 301);
Route::redirect('/plantilla', '/', 301);
Route::redirect('/convocatorias', '/convocatoria', 301);
Route::redirect('/convoctaria', '/convocatoria', 301);
Route::redirect('/contacto', '/', 301);

// Slugs de restaurante que cambiaron en el nuevo Laravel
Route::redirect('/restaurantes/curich', '/restaurantes/cremoladas-curich', 301);
Route::redirect('/restaurantes/anticuching', '/restaurantes/anticuchos-anticuching', 301);
Route::redirect('/restaurantes/saltao', '/restaurantes/saltao-wok-food', 301);
Route::redirect('/restaurantes/bros', '/restaurantes/broaster-bros', 301);
Route::redirect('/restaurantes/la-limanesa', '/restaurantes/limanesas', 301);
Route::redirect('/restaurantes/sisa', '/restaurantes/sisa-coffee-wine', 301);
Route::redirect('/restaurantes/puerto-mancora', '/restaurantes/barrio-mancora', 301);
Route::redirect('/restaurantes/cavenecia-2', '/restaurantes/cavenecia', 301);

// Restaurantes retirados (sin equivalente en el nuevo sitio) → listado
Route::redirect('/restaurantes/la-choza-de-la-anaconda', '/restaurantes', 301);
Route::redirect('/restaurantes/caja-china-criolla', '/restaurantes', 301);

// Etiquetas de restaurante del WP (taxonomía sin equivalente en Laravel) → listado
Route::redirect('/etiquetas-restaurant/{any}', '/restaurantes', 301)->where('any', '.*');

// Eventos pasados del WP → índice de eventos (evita 404 en links viejos)
Route::redirect('/eventos/la-banda-del-chino', '/eventos', 301);
Route::redirect('/eventos/stereobit', '/eventos', 301);
Route::redirect('/eventos/frank-ariel', '/eventos', 301);
Route::redirect('/eventos/the-tropical-band', '/eventos', 301);
Route::redirect('/eventos/tributo-a-diego-torres', '/eventos', 301);
Route::redirect('/eventos/jeff-gaban', '/eventos', 301);
Route::redirect('/eventos/jean-soto', '/eventos', 301);
Route::redirect('/eventos/latin-groove', '/eventos', 301);

Route::get('/', HomeController::class)->name('home');

Route::get('/restaurantes', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurantes/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/eventos', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/preguntas-frecuentes', [StaticPageController::class, 'faq'])->name('static.faq');
Route::get('/reglamento-pet-friendly', [StaticPageController::class, 'petFriendly'])->name('static.pet-friendly');
Route::get('/politica-de-estacionamiento', [StaticPageController::class, 'parking'])->name('static.parking');
Route::get('/descuentos-u-lima', [StaticPageController::class, 'ulima'])->name('static.ulima');

Route::get('/convocatoria', [MirroredPageController::class, 'convocatoria'])->name('convocatoria');
Route::get('/terminos-y-condiciones', [MirroredPageController::class, 'terms'])->name('legal.terms');
Route::get('/politica-privacidad', [MirroredPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/politicas-de-privacidad', [MirroredPageController::class, 'privacy']);
Route::get('/libro-de-reclamaciones', [MirroredPageController::class, 'complaintsBook'])->name('legal.complaints');
Route::post('/libro-de-reclamaciones', [MirroredPageController::class, 'storeComplaint'])->name('legal.complaints.store');
Route::post('/informacion', [MirroredPageController::class, 'storeInquiry'])->name('info.store');
Route::get('/nosotros', AboutController::class)->name('about');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');

    if (! is_file($path)) {
        abort(404);
    }

    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap.xml');
