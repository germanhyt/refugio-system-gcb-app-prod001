<?php

namespace App\Http\Controllers;

use App\Models\ContactBlock;
use App\Models\VisitInfo;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $visit = VisitInfo::current();
        $visit->load('media');

        return view('pages.about', [
            'visit' => $visit,
            'aboutGalleryImages' => $visit->aboutGallerySlides(),
        ]);
    }
}
