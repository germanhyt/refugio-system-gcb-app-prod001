<?php

use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_info', function (Blueprint $table) {
            $table->string('about_eyebrow', 255)->nullable()->after('about_content');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('complaint_book_recipients')->nullable()->after('hero_title_services');
            $table->string('mail_from_name')->nullable()->after('complaint_book_recipients');
            $table->string('mail_from_address')->nullable()->after('mail_from_name');
            $table->string('mail_host')->nullable()->after('mail_from_address');
            $table->unsignedInteger('mail_port')->nullable()->after('mail_host');
            $table->string('mail_username')->nullable()->after('mail_port');
            $table->text('mail_password')->nullable()->after('mail_username');
            $table->string('mail_encryption', 20)->nullable()->after('mail_password');
        });

        VisitInfo::query()->whereKey(1)->update([
            'about_eyebrow' => VisitInfo::DEFAULT_HOLA_EYEBROW,
        ]);

        $settings = SiteSetting::query()->find(1);
        if ($settings) {
            $settings->forceFill([
                'complaint_book_recipients' => $settings->complaint_book_recipients ?: [
                    'leilah@gcb.pe',
                    'mario@refugiogastronomico.pe',
                    'nataly@gcb.pe',
                ],
                'mail_from_name' => $settings->mail_from_name ?: config('mail.from.name'),
                'mail_from_address' => $settings->mail_from_address ?: config('mail.from.address'),
            ])->save();

            $banners = [
                'restaurants' => public_path('images/nuevo/banners/que-te-provoca-hoy.jpg'),
                'services' => public_path('images/nuevo/banners/nuestros-servicios.jpg'),
                'events' => public_path('images/nuevo/banners/somos-elrefugio-de-tu-diversion.jpg'),
            ];

            foreach ($banners as $page => $path) {
                $collection = 'hero_'.$page;
                if ($settings->getFirstMedia($collection)) {
                    continue;
                }
                if (! is_file($path)) {
                    continue;
                }
                $settings->addMedia($path)
                    ->preservingOriginal()
                    ->toMediaCollection($collection);
            }
        }
    }

    public function down(): void
    {
        Schema::table('visit_info', function (Blueprint $table) {
            $table->dropColumn('about_eyebrow');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'complaint_book_recipients',
                'mail_from_name',
                'mail_from_address',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
            ]);
        });
    }
};
