<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@refugio.pe'],
            [
                'name' => 'Admin Refugio',
                'password' => Hash::make('RefugioAdmin2026!'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        $this->call([
            RestaurantCategorySeeder::class,
            CtaBlockSeeder::class,
            VisitInfoSeeder::class,
            ServiceItemSeeder::class,
            EventOfferSeeder::class,
            ContactBlockSeeder::class,
            HeroSlideSeeder::class,
            StaticPageSeeder::class,
            RestaurantDirectorySeeder::class,
        ]);
    }
}
