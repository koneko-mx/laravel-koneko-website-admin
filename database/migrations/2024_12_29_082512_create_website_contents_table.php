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
        Schema::create('website_contents', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedSmallInteger('site_id')->index();
            $table->string('type', 16)->default('page')->index();   // Enum: Content, Landing, Product, Category Blog, Partial

            // Metadata
            $table->string('title')->index();                       // Título del website
            $table->string('slug')->index();
            $table->mediumText('description')->nullable();          // Descripción del website
            $table->json('keywords')->nullable();

            // Canonical URL
            $table->string('canonical_url')->nullable();

            // Robots Directives
            $table->boolean('noindex')->default(false)->index();
            $table->boolean('nofollow')->default(false)->index();

            // Bloques de contenido (JSON)
            $table->json('header_blocks')->nullable();
            $table->json('content_blocks')->nullable();
            $table->json('footer_blocks')->nullable();

            // Visibilidad
            $table->json('roles')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('hide_if_authenticated')->default(false)->index();
            $table->boolean('hide_if_guest')->default(false)->index();
            $table->dateTime('visible_from')->nullable();
            $table->dateTime('visible_until')->nullable();

            // Estatus
            $table->string('status', 16)->default('draft')->index();

            // Cache
            $table->boolean('enable_cache')->default(true)->index();
            $table->unsignedSmallInteger('cache_ttl')->nullable();

            // Auditoria
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // Indices
            $table->unique(['site_id', 'slug']);
            $table->index(['site_id', 'status']);
            $table->index(['site_id', 'status', 'slug']);
            $table->index(['site_id', 'status', 'visible_from', 'visible_until']);

            // Relaciones
            $table->foreign('site_id')->references('id')->on('website_sites')->cascadeOnDelete();

            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('website_sites', function (Blueprint $table) {
            $table->foreign('coming_soon_content_id')
                ->references('id')->on('website_contents')->nullOnDelete();
            $table->foreign('maintenance_content_id')
                ->references('id')->on('website_contents')->nullOnDelete();
        });
   }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_sites', function (Blueprint $table) {
            $table->dropForeign(['coming_soon_content_id']);
            $table->dropForeign(['maintenance_content_id']);
        });
        Schema::dropIfExists('website_contents');
    }
};
