<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/restaurantes', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurantes/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/eventos', [EventController::class, 'index'])->name('events.index');
Route::get('/eventos/{event:slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/contacto', ContactController::class)->name('contact');
Route::get('/nosotros', AboutController::class)->name('about');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
