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
        Schema::create('website_sites', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Identidad
            $table->string('domain')->unique();              // Dominio principal (sin protocolo)

            // Formato de Titulo
            $table->string('title', 96)->index();

            // Autor, Brand y Copyright
            $table->string('brand_name', 70)->nullable();
            $table->string('slogan')->nullable();

            // Robots Directives
            $table->string('robots_mode', 16)->default('content')->index(); // ['suspended','site','content']

            $table->boolean('www_redirect')->default(true);
            $table->boolean('force_https')->default(true);

            // Estado
            $table->string('status', 16)->default('coming_soon')->index();

            // Páginas especiales
            $table->unsignedMediumInteger('coming_soon_content_id')->nullable()->index();
            $table->unsignedMediumInteger('maintenance_content_id')->nullable()->index();

            // Auditoría
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();
            $table->timestamps();

            // Relaciones
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_sites');
    }
};
