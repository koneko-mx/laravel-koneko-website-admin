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
        Schema::create('website_menu_items', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('menu_id')->index();
            $table->unsignedSmallInteger('parent_id')->nullable()->index();

            $table->json('title');                                      // i18n multilanguage
            $table->string('type', 16)->default('cms_page')->index();   // Enum; cms_page | url | laravel_route | blog_article | Evento Json

            // Construcción de enlace
            $table->unsignedMediumInteger('linkable_id')->nullable()->index();      // Relación polimórfica con:
            $table->string('linkable_type')->nullable()->index();                   // páginas, entradas, productos, etc.

            $table->string('laravel_route')->nullable()->index();
            $table->string('url')->nullable()->index();
            $table->string('method')->nullable();
            $table->string('target', 16)->nullable();                   // Enum _self, _blank, etc.
            $table->string('js_event')->nullable();

            // UI
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_color')->nullable();

            // Visibilidad
            $table->json('roles')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('hide_if_authenticated')->default(false)->index();
            $table->boolean('hide_if_guest')->default(false)->index();
            $table->timestamp('visible_from')->nullable();
            $table->timestamp('visible_until')->nullable();

            $table->unsignedSmallInteger('order')->default(0);          // Para ordenar en el menú
            $table->boolean('is_active')->default(true)->index();

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // Indices
            $table->index(['menu_id', 'is_active']);
            $table->index(['menu_id', 'parent_id', 'is_active']);
            $table->index(['linkable_id', 'linkable_type']);

            // Relaciones
            $table->foreign('menu_id')->references('id')->on('website_menus')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('website_menu_items')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_menu_items');
    }
};
