<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\HeroSlide;
use App\Models\HomeRestaurantFeature;
use App\Models\InstagramPost;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'slides' => HeroSlide::query()->active()->ordered()->with('media')->get(),
            'featuredRestaurants' => HomeRestaurantFeature::query()
                ->active()
                ->ordered()
                ->with(['restaurant.media'])
                ->whereHas('restaurant', fn ($q) => $q->active())
                ->get(),
            'events' => Event::query()->active()->orderBy('event_date')->with('media')->take(12)->get(),
            'blogPosts' => BlogPost::query()->active()->featured()->ordered()->with('media')->take(4)->get(),
            'instagramPosts' => InstagramPost::query()->active()->ordered()->with('media')->take(12)->get(),
        ]);
    }
}
