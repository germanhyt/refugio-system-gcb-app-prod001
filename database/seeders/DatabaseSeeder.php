<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@refugio.pe'],
            [
                'name' => 'Admin Refugio',
                'password' => Hash::make((string) (env('ADMIN_SEED_PASSWORD') ?: Str::password(24))),
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
            RestaurantCategoryAssignmentSeeder::class,
            RestaurantParkAssetsSeeder::class,
            RestaurantMapSeeder::class,
            LocalMediaImportSeeder::class,
            SiteDocumentSeeder::class,
        ]);
    }
}
