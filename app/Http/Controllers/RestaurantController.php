<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = trim($request->string('categoria')->toString());

        $categories = RestaurantCategory::query()
            ->active()
            ->withCount(['restaurants' => fn ($q) => $q->active()])
            ->orderBy('sort_order')
            ->get();

        $activeCategory = $categorySlug !== ''
            ? $categories->firstWhere('slug', $categorySlug)
            : null;

        // Invalid slug → treat as "Todos"
        if ($categorySlug !== '' && ! $activeCategory) {
            $categorySlug = '';
        }

        $restaurants = Restaurant::query()
            ->active()
            ->with(['media', 'categories'])
            ->when($activeCategory, function ($query) use ($activeCategory) {
                $query->whereHas(
                    'categories',
                    fn ($q) => $q->where('restaurant_categories.id', $activeCategory->id)
                );
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('pages.restaurants.index', compact('restaurants', 'categories', 'categorySlug'));
    }

    public function show(Restaurant $restaurant): View
    {
        abort_unless($restaurant->is_active, 404);

        $restaurant->load(['media', 'categories']);

        $categoryIds = $restaurant->categories->pluck('id');

        $similarRestaurants = Restaurant::query()
            ->active()
            ->whereKeyNot($restaurant->id)
            ->when(
                $categoryIds->isNotEmpty(),
                fn ($query) => $query->whereHas(
                    'categories',
                    fn ($q) => $q->whereIn('restaurant_categories.id', $categoryIds)
                ),
                fn ($query) => $query->whereRaw('0 = 1')
            )
            ->with(['media', 'categories'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('pages.restaurants.show', compact('restaurant', 'similarRestaurants'));
    }
}
