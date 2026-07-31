<?php

namespace App\Http\Controllers;

use App\Models\VisitInfo;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'visit' => VisitInfo::current(),
        ]);
    }
}
