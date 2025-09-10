<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_seo_profiles', function (Blueprint $table) {
            $table->mediumIncrements('id');

            // Dueño polimórfico
            $table->string('seoable_type', 191)->index();
            $table->unsignedMediumInteger('seoable_id')->index();
            $table->string('scope', 16)->default('content')->index();           // 'site' | 'content'

            // Autor y Copyright
            $table->string('author_mode', 12)->default('site')->index();        // site|content|disable
            $table->string('author')->nullable();                               // Autor del website

            $table->string('copyright_mode', 12)->default('site')->index();     // site|content|disable
            $table->string('copyright')->nullable();                            // Copyright del website

            // Schema.org
            $table->string('schema_mode', 12)->default('disable')->index();     // site|content|disable
            $table->json('schema_org')->nullable();

            // Favicon
            $table->string('favicon_mode', 12)->default('site')->index();       // site|content|disable
            $table->json('favicon')->nullable();

            // Formato de Titulo
            $table->string('title_mode', 12)->default('site')->index();         // site|content|disable
            $table->string('title_format', 12)->nullable()->index();       // site|content|disable

            // Template por defecto
            $table->string('template_mode', 12)->default('site')->index();      // site|content|disable
            $table->string('package')->nullable()->index();
            $table->string('layout')->nullable()->index();
            $table->string('theme_color', 16)->nullable();

            // Locale
            $table->string('locale_mode', 12)->default('site')->index();        // site|content|disable
            $table->string('locale', 8)->nullable();

            // Open Graph
            $table->string('og_mode', 12)->default('site')->index();            // site|content|disable
            $table->string('og_type')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_url')->nullable();
            $table->string('og_site_name')->nullable();

            // Twitter Card
            $table->string('twitter_mode', 12)->default('site')->index();       // site|content|disable
            $table->string('twitter_card')->nullable();
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_creator')->nullable();

            // Indices
            $table->unique(['seoable_type','seoable_id']);
            $table->unique(['scope', 'seoable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_seo_profiles');
    }
};
