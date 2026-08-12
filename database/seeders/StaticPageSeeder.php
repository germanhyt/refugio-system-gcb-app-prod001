<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('static-pages', []) as $slug => $page) {
            StaticPage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'intro' => $page['intro'] ?? null,
                    'blocks' => $page['blocks'] ?? [],
                ]
            );
        }
    }
}
