<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MirroredPageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

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

Route::get('/contacto', ContactController::class)->name('contact');
Route::get('/convocatoria', [MirroredPageController::class, 'convocatoria'])->name('convocatoria');
Route::get('/convocatorias', [MirroredPageController::class, 'convocatoria']);
Route::get('/convoctaria', [MirroredPageController::class, 'convocatoria']);
Route::get('/terminos-y-condiciones', [MirroredPageController::class, 'terms'])->name('legal.terms');
Route::get('/politica-privacidad', [MirroredPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/politicas-de-privacidad', [MirroredPageController::class, 'privacy']);
Route::get('/libro-de-reclamaciones', [MirroredPageController::class, 'complaintsBook'])->name('legal.complaints');
Route::post('/libro-de-reclamaciones', [MirroredPageController::class, 'storeComplaint'])->name('legal.complaints.store');
Route::post('/informacion', [MirroredPageController::class, 'storeInquiry'])->name('info.store');
Route::get('/nosotros', AboutController::class)->name('about');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
