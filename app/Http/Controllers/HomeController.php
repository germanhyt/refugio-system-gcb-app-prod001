<?php

namespace App\Http\Controllers;

use App\Models\ContactBlock;
use App\Models\HeroSlide;
use App\Models\HomeRestaurantFeature;
use App\Models\ServiceItem;
use App\Services\SeoService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly SeoService $seo)
    {
    }

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
            'homeServices' => ServiceItem::query()
                ->active()
                ->showOnHome()
                ->ordered()
                ->with('media')
                ->limit(8)
                ->get(),
            'contactBlocks' => ContactBlock::query()->active()->ordered()->get(),
            'jsonLd' => $this->seo->organizationJsonLd(
                \App\Models\SiteSetting::current(),
                \App\Models\VisitInfo::current()
            ),
        ]);
    }
}
