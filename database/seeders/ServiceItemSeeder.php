<?php

namespace Database\Seeders;

use App\Models\ServiceItem;
use Illuminate\Database\Seeder;

class ServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'WhatsApp Refugio', 'icon_key' => 'whatsapp', 'description' => '¡Conversemos: 991 318 720!', 'contact_phone' => '991 318 720', 'show_on_home' => true],
            ['title' => 'Estacionamiento gratis', 'icon_key' => 'parking', 'description' => '3 horas, por consumos mayores a 50 soles', 'show_on_home' => true],
            ['title' => 'Pet friendly', 'icon_key' => 'pet', 'description' => 'Tu mascota es bienvenida', 'show_on_home' => true],
            ['title' => 'Espacios para eventos', 'icon_key' => 'event-spaces', 'description' => 'Organiza tu evento social o corporativo: 994 848 723', 'contact_phone' => '994 848 723', 'show_on_home' => true],
            ['title' => 'Catering', 'icon_key' => 'catering', 'description' => 'Llevamos el sabor a tu evento: 994 848 723', 'contact_phone' => '994 848 723', 'show_on_home' => true],
            ['title' => 'Bosque Mágico', 'icon_key' => 'kids-zone', 'description' => 'Zona infantil', 'show_on_home' => true],
            ['title' => 'Espacios publicitarios', 'icon_key' => 'ads', 'description' => '¡Muestra tu marca en Refugio! 994 848 723', 'contact_phone' => '994 848 723', 'show_on_home' => true],
            ['title' => 'Delivery', 'icon_key' => 'delivery', 'description' => '¡Llegamos hasta donde estés!', 'show_on_home' => true],
            ['title' => 'Shows en vivo', 'icon_key' => 'live-shows', 'description' => 'Revisa nuestro cronograma mensual', 'show_on_home' => false],
            ['title' => 'Shows infantiles', 'icon_key' => 'kids-shows', 'description' => 'Revisa nuestro cronograma mensual', 'show_on_home' => false],
            ['title' => 'Objetos perdidos', 'icon_key' => 'lost-found', 'description' => 'Tu objeto puede estar aquí: 997 960 902', 'contact_phone' => '997 960 902', 'show_on_home' => false],
            ['title' => 'Baños para niños', 'icon_key' => 'kids-restroom', 'description' => 'Baños exclusivos para niños.', 'show_on_home' => false],
            ['title' => 'Servicios Higiénicos', 'icon_key' => 'restrooms', 'description' => 'Libre para todos nuestros visitantes', 'show_on_home' => false],
            ['title' => 'Tópico', 'icon_key' => 'emergency', 'description' => 'Atención de primeros auxilios', 'show_on_home' => false],
        ];

        $keepKeys = [];

        foreach ($items as $index => $item) {
            $keepKeys[] = $item['icon_key'];

            ServiceItem::query()->updateOrCreate(
                ['icon_key' => $item['icon_key']],
                [
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'contact_phone' => $item['contact_phone'] ?? null,
                    'sort_order' => $index + 1,
                    'show_on_home' => $item['show_on_home'],
                    'is_active' => true,
                ]
            );
        }

        ServiceItem::query()
            ->whereNotIn('icon_key', $keepKeys)
            ->update(['is_active' => false, 'show_on_home' => false]);
    }
}
