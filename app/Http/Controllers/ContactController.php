<?php

namespace App\Http\Controllers;

use App\Models\ContactBlock;
use App\Models\VisitInfo;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.contact', [
            'visit' => VisitInfo::current(),
            'contactBlocks' => ContactBlock::query()->active()->ordered()->get(),
        ]);
    }
}
