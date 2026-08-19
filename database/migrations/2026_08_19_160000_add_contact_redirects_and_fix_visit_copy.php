<?php

use App\Models\VisitInfo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_blocks', function (Blueprint $table) {
            $table->boolean('redirects_enabled')->default(true)->after('is_active');
        });

        foreach (VisitInfo::query()->get() as $visit) {
            $copy = $visit->holaCopy();

            $visit->forceFill([
                'map_embed_url' => VisitInfo::normalizeMapEmbedUrl($visit->map_embed_url),
                'about_content' => VisitInfo::composeAboutContent($copy['headline'], $copy['body']),
            ])->save();
        }
    }

    public function down(): void
    {
        Schema::table('contact_blocks', function (Blueprint $table) {
            $table->dropColumn('redirects_enabled');
        });
    }
};
