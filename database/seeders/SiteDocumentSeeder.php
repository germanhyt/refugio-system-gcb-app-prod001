<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SiteDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            public_path('images/ULIMA-DESCUENTOS.pdf'),
            public_path('images/nuevo/ULIMA-DESCUENTOS.pdf'),
        ];

        $source = collect($sources)->first(fn (string $path): bool => File::exists($path));

        if (! $source) {
            return;
        }

        $publicCopy = public_path('images/ULIMA-DESCUENTOS.pdf');

        if ($source !== $publicCopy) {
            File::ensureDirectoryExists(dirname($publicCopy));
            File::copy($source, $publicCopy);
            $source = $publicCopy;
        }

        $settings = SiteSetting::current();
        $settings->clearMediaCollection('ulima_discounts_pdf');
        $settings
            ->addMedia($source)
            ->preservingOriginal()
            ->toMediaCollection('ulima_discounts_pdf');
    }
}
