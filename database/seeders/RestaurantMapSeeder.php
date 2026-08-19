<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\SiteSetting;
use App\Support\PublicMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RestaurantMapSeeder extends Seeder
{
    public const SOURCE_DIR = 'C:\\Users\\gcbso\\Documents\\WEB_REFUGIO_ASSETS\\MAPA WEB 1';

    /**
     * @var array<string, string>
     */
    public const SOURCE_FILES = [
        'ahumare' => 'AHUMARE.svg',
        'anticuchos-anticuching' => 'ANTICUCHING.svg',
        'barrio-mancora' => 'BARRIO MANCORA.svg',
        'barrio-wok' => 'BARRIO WOK.svg',
        'broaster-bros' => 'BROS.svg',
        'caldos-doris' => 'CALDOS DORIS.svg',
        'cavenecia' => 'CAVENECIA.svg',
        'cremoladas-curich' => 'CURICH.svg',
        'don-melchor' => 'DON MELCHOR.svg',
        'hanzo-express' => 'HANZO.svg',
        'la-22-sangucheria' => 'LA 22.svg',
        'la-victoria' => 'LA VICTORIA.svg',
        'lili-blue' => 'LILI BLUE.svg',
        'limanesas' => 'LIMANESAS.svg',
        'madre-amazonica' => 'MADRE SELVA.svg',
        'mr-smash' => 'MR. SMASH.svg',
        'nashmys' => 'NASHMYS.svg',
        'ramen-ya' => 'RAMEN YA!.svg',
        'saltao-wok-food' => 'SALTAO.svg',
        'sisa-coffee-wine' => 'SISA.svg',
        'tortas-gaby' => 'TORTAS GABY.svg',
    ];

    public function run(): void
    {
        $this->copySourceMaps();

        foreach (Restaurant::query()->get() as $restaurant) {
            PublicMedia::syncPublic(
                $restaurant,
                'location_image',
                config('restaurant-assets.park_maps.'.$restaurant->slug)
            );
        }

        $settings = SiteSetting::current();

        foreach ([
            'about' => 'images/refugio/nosotros-banner.png',
            'restaurants' => 'images/nuevo/banners/que-te-provoca-hoy.jpg',
            'events' => 'images/nuevo/banners/somos-elrefugio-de-tu-diversion.jpg',
            'services' => 'images/nuevo/banners/nuestros-servicios.jpg',
            'complaints' => 'images/refugio/bg_contacto-home.jpg',
        ] as $page => $relative) {
            if ($settings->getFirstMedia('hero_'.$page)) {
                continue;
            }

            PublicMedia::syncPublic($settings, 'hero_'.$page, $relative);
        }
    }

    private function copySourceMaps(): void
    {
        $sourceDir = self::SOURCE_DIR;
        if (! is_dir($sourceDir)) {
            return;
        }

        $destDir = public_path('images/nuevo/MAPAS');
        File::ensureDirectoryExists($destDir);

        foreach (self::SOURCE_FILES as $slug => $filename) {
            $from = $sourceDir.DIRECTORY_SEPARATOR.$filename;
            $relative = config('restaurant-assets.park_maps.'.$slug);
            if (! is_file($from) || ! is_string($relative)) {
                continue;
            }

            File::copy($from, public_path($relative));
        }
    }
}
