<?php

use App\Models\ServiceItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->string('contact_phone', 40)->nullable()->after('description');
            $table->string('whatsapp_message', 255)->nullable()->after('contact_phone');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_fixed_social')->default(true)->after('show_blog_section');
        });

        foreach (ServiceItem::query()->get() as $item) {
            if (filled($item->contact_phone)) {
                continue;
            }

            if (preg_match('/\b(\d{3}(?:\s\d{3}){2}|\d{9})\b/', (string) $item->description, $match)) {
                $item->forceFill(['contact_phone' => $match[1]])->save();
            }
        }
    }

    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn(['contact_phone', 'whatsapp_message']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('show_fixed_social');
        });
    }
};
