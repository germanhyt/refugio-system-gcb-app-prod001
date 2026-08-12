<?php

namespace App\Http\Controllers;

use App\Models\ServiceItem;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = ServiceItem::query()
            ->active()
            ->ordered()
            ->with('media')
            ->get();

        return view('pages.services.index', compact('services'));
    }
}
