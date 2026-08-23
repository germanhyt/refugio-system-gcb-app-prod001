<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\StaticPage;
use App\Services\SeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function __construct(private readonly SeoService $seo)
    {
    }

    public function faq(): View
    {
        return $this->render('faq');
    }

    public function petFriendly(): View
    {
        return $this->render('pet-friendly');
    }

    public function parking(): View
    {
        return $this->render('parking');
    }

    public function ulima(): View|RedirectResponse
    {
        $pdf = SiteSetting::current()->ulimaDiscountsPdfUrl();

        if (filled($pdf)) {
            return redirect()->away($pdf);
        }

        return $this->render('ulima');
    }

    private function render(string $key): View
    {
        $page = StaticPage::findBySlug($key);

        $pageArray = $page?->toPageArray() ?? config("static-pages.{$key}", []);

        $faqLd = '';
        if ($key === 'faq') {
            $faqLd = $this->seo->faqJsonLd($pageArray);
        }

        return view('pages.static.show', [
            'page' => $pageArray,
        ])->with('faqLd', $faqLd);
    }
}
