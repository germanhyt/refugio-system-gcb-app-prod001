<?php

namespace Database\Seeders;

use App\Models\HomeRestaurantFeature;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantDirectorySeeder extends Seeder
{
    public function run(): void
    {
        $officialSlugs = [];

        foreach ($this->rows() as $row) {
            $restaurant = Restaurant::query()->where('slug', $row['slug'])->first()
                ?? Restaurant::query()
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($row['match']).'%'])
                    ->first();

            $payload = [
                'name' => $row['name'],
                'short_description' => $row['short_description'],
                'website_url' => $row['website_url'],
                'reservation_phone' => $row['reservation_phone'],
                'whatsapp_url' => $row['whatsapp_url'],
                'instagram_url' => $row['instagram_url'],
                'tiktok_url' => $row['tiktok_url'],
                'delivery_peya_enabled' => filled($row['delivery_peya_url']),
                'delivery_peya_url' => $row['delivery_peya_url'],
                'delivery_rappi_enabled' => filled($row['delivery_rappi_url']),
                'delivery_rappi_url' => $row['delivery_rappi_url'],
                'corporate_discount_mode' => $row['discount']
                    ? Restaurant::DISCOUNT_BADGE
                    : Restaurant::DISCOUNT_NONE,
                'corporate_discounts' => [],
                'is_active' => true,
            ];

            if ($restaurant) {
                $restaurant->update($payload);
            } else {
                $restaurant = Restaurant::query()->create([
                    ...$payload,
                    'slug' => $row['slug'],
                ]);
            }

            $officialSlugs[] = $restaurant->slug;
        }

        Restaurant::query()
            ->whereNotIn('slug', $officialSlugs)
            ->update(['is_active' => false]);

        HomeRestaurantFeature::query()
            ->whereHas('restaurant', fn ($query) => $query->where('is_active', false))
            ->update(['is_active' => false]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            $this->row('cavenecia', 'Cavenecia', 'Cavenecia Steakhouse', 'Carnes y parrillas con cortes Angus importados de USA', 'https://caveneciasteakhouse.com', '939010993', 'https://www.instagram.com/cavenecia.steakhouse/', 'https://www.tiktok.com/@cavenecia.steakhouse', 'https://www.pedidosya.com.pe/restaurantes/lima/cavenecia-steakhouse--refugio-a2309cb5-b3b5-4419-b071-ffea5a679576-menu', null, true),
            $this->row('barrio-mancora', 'Barrio Mancora', 'Barrio Mancora', 'Ceviches, pescados y sabores de nuestra cocina criolla', null, '961788255', 'https://www.instagram.com/barriomancora/', 'https://www.tiktok.com/@barrio.mancora', null, 'https://www.rappi.com.pe/restaurantes/delivery/67792-barrio-mancora', true),
            $this->row('sisa-coffee-wine', 'Sisa', 'Sisa', 'Café, desayunos y sabores para cualquier momento del día.', null, '994848999', 'https://www.instagram.com/sisa_cafebistro/', 'https://www.tiktok.com/@sisa.coffee', 'https://www.pedidosya.com.pe/restaurantes/lima/sisa-coffee-wine-refugio-gastronomico-3960c96f-8d04-4273-b556-90f5852f677d-menu', 'https://www.rappi.com.pe/restaurantes/delivery/56882-sisa', true),
            $this->row('refugio-bar', 'Refugio Bar', 'Refugio Bar', 'El bar perfecto para brindar, compartir y disfrutar.', null, '980541946', 'https://www.instagram.com/refugiobar.oficial/', null, null, null, false),
            $this->row('don-melchor', 'Don Melchor', 'Don Melchor', 'Tradición, innovación y sabor en cada Pollo a la Brasa y Parrilla.', 'https://donmelchorpollos.com', '923264129', 'https://www.instagram.com/donmelchorpollos/', 'https://www.tiktok.com/@donmelchorpollos', 'https://www.pedidosya.com.pe/restaurantes/lima/don-melchor-c4fc4f1f-3f3c-4f9d-975b-c2babb363748-menu', 'https://www.rappi.com.pe/restaurantes/delivery/39927-don-melchor', false),
            $this->row('ahumare', 'Ahumare', 'Ahumare', 'Ahumados y salteados con ese toque de humo que lo cambia todo.', null, null, 'https://www.instagram.com/ahu.mare/', 'https://www.tiktok.com/@ahumare2025', null, null, true),
            $this->row('anticuchos-anticuching', 'Anticuching', 'Anticuching', 'Para nosotros todo es anticuchable, somos las brochetas más largas del Perú.', null, null, 'https://www.instagram.com/anticuching/?hl=es', 'https://www.tiktok.com/@anticuching_byv', null, 'https://www.rappi.com.pe/restaurantes/delivery/5681-anticuching', true),
            $this->row('madre-amazonica', 'Madre Amazónica', 'Madre Amazónica', 'Sabores de la selva, la cocina criolla y el mar, reunidos en un solo lugar.', null, null, 'https://www.instagram.com/madre_amazonica/?hl=es', null, null, null, false),
            $this->row('la-22-sangucheria', 'La 22', 'La 22', 'Hamburguesas, salchipapas & más', null, null, 'https://www.instagram.com/la22sangucheria/', null, 'https://www.pedidosya.com.pe/restaurantes/lima/la-22-sangucheria-refugio-eb52fadf-e749-4617-bd4f-fbc33637ee5c-menu', 'https://www.rappi.com.pe/restaurantes/delivery/5636-la-22-sangucheria', true),
            $this->row('la-victoria', 'La Victoria', 'La Victoria', 'Sanguches criollos con sabor peruano en cada bocado.', null, null, 'https://www.instagram.com/lavictoria.pe/', null, null, null, false),
            $this->row('tortas-gaby', 'Tortas Gaby', 'Tortas Gaby', 'Tortas y dulces hechos para celebrar cada momento.', 'https://www.tortasgaby.com.pe', null, 'https://www.instagram.com/tortasgabyoficial/', 'https://www.tiktok.com/@tortasgabyoficial', 'https://www.pedidosya.com.pe/restaurantes/lima/tortas-gaby--refugio-39d03a92-56b5-476b-90c3-003413c80052-menu', 'https://www.rappi.com.pe/restaurantes/delivery/18683-pasteleria-tortas-gaby', false),
            $this->row('barrio-wok', 'Barrio Wok', 'Barrio Wok', 'Chifa de barrio, wok al fuego y ese olor a chifa que no se olvida.', null, null, 'https://www.instagram.com/chifabarriowok/', 'https://www.tiktok.com/@chifabarriowok', 'https://www.pedidosya.com.pe/restaurantes/lima/barrio-wok-el-refugio-3d6b9ba5-d1d4-4600-bea0-ee5d54382d4a-menu', 'https://www.rappi.com.pe/restaurantes/delivery/67829-barrio-wok', true),
            $this->row('lili-blue', 'Lili Blue', 'Lili Blue', 'Comida peruana', null, null, null, null, null, null, false),
            $this->row('saltao-wok-food', 'Saltao', 'Saltao', 'Saltados criollos con todo el sabor y tradición peruana.', null, null, 'https://www.instagram.com/saltao.peru/', 'https://www.tiktok.com/@saltao.peru', 'https://www.pedidosya.com.pe/restaurantes/lima/saltao--el-refugio-73c53236-e10c-40c6-9323-917d0e601475-menu', null, false),
            $this->row('broaster-bros', 'Bros', 'Bros', 'El verdadero pollo crunch, crujiente, sabroso y adictivo.', null, null, 'https://www.instagram.com/broslima/', null, null, null, false),
            $this->row('ramen-ya', 'Ramen Ya!', 'Ramen Ya!', 'Ramen al estilo Hanzo, con sabores japoneses que conquistan en cada bowl.', null, null, 'https://www.instagram.com/hanzoxpress/', null, null, 'https://www.rappi.com.pe/restaurantes/70467-ramen-ya-by-hanzo', true),
            $this->row('hanzo-express', 'Hanzo', 'Hanzo', 'Street food nikkei, rápido y delicioso, con tus platos favoritos fríos y calientes.', null, null, 'https://www.instagram.com/hanzoxpress/', null, null, null, true),
            $this->row('mr-smash', 'Mr Smash', 'Mr. Smash', 'La verdadera smash burger: jugosa, crujiente y llena de sabor.', null, null, 'https://www.instagram.com/mrsmash.pe/', 'https://www.tiktok.com/@mrsmash.pe', 'https://www.pedidosya.com.pe/restaurantes/lima/mr-smash-burger--el-refugio-8b5f33d3-1a52-49b4-9063-221f140afdc6-menu', 'https://www.rappi.com.pe/restaurantes/delivery/46022-mr-smash', true),
            $this->row('caldos-doris', 'Caldos Doris', 'Caldos Doris', 'El auténtico caldo de gallina, preparado con tradición, calidad y ese sabor casero que siempre provoca volver.', null, null, 'https://www.instagram.com/caldosdoris/', 'https://www.tiktok.com/@caldosdoris', 'https://www.pedidosya.com.pe/restaurantes/lima/caldos-doris-refugio-6b617db6-1ec6-432b-b1aa-a16e83428f4e-menu', 'https://www.rappi.com.pe/restaurantes/delivery/48981-caldos-doris', true),
            $this->row('limanesas', 'Limanesas', 'Limanesas', 'Milanesas crujientes, hechas al momento y llenas de sabor.', 'https://www.limanesas.com', null, 'https://www.instagram.com/limanesas/', 'https://www.tiktok.com/@limanesas', 'https://www.pedidosya.com.pe/restaurantes/lima/limanesas-refugio-32b5061d-7ca3-437b-892c-3c6e4365540b-menu', 'https://www.rappi.com.pe/restaurantes/76162-limanesas', true),
            $this->row('nashmys', 'Nashmy', 'Nashmys', 'Comida Árabe Rápida', null, null, 'https://www.instagram.com/nashmys.pe/', 'https://www.tiktok.com/@_nashmys.pe', 'https://www.pedidosya.com.pe/restaurantes/lima/nashmys-el-refugio-3a3b8b75-6b73-4685-99ae-6c7c0e4f7f28-menu', 'https://www.rappi.com.pe/restaurantes/delivery/52260-nashmys-comida-arabe', true),
            $this->row('cremoladas-curich', 'Curich', 'Curich', 'Cremoladas con calidad, sabor y tradición desde 1942.', 'https://www.cremoladascurich.com', null, 'https://www.instagram.com/curichcremoladas/', 'https://www.tiktok.com/@curichcremoladas', null, null, false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function row(
        string $slug,
        string $match,
        string $name,
        string $short,
        ?string $website,
        ?string $phone,
        ?string $instagram,
        ?string $tiktok,
        ?string $peya,
        ?string $rappi,
        bool $discount,
    ): array {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return [
            'slug' => $slug,
            'match' => $match,
            'name' => $name,
            'short_description' => $short,
            'website_url' => $website,
            'reservation_phone' => $phone,
            'whatsapp_url' => strlen((string) $digits) === 9 ? 'https://wa.me/51'.$digits : null,
            'instagram_url' => $instagram,
            'tiktok_url' => $tiktok,
            'delivery_peya_url' => $peya,
            'delivery_rappi_url' => $rappi,
            'discount' => $discount,
        ];
    }
}
