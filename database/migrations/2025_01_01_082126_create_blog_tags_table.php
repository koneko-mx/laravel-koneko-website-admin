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
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('site_id')->index();        // Soporte multisite

            $table->string('name')->index();
            $table->string('slug')->index();

            $table->boolean('is_active')->default(true)->index();

            // Auditoria
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // Indices
            $table->unique(['slug', 'site_id']);
            $table->index(['name', 'is_active']);

            // Relaciones
            $table->foreign('site_id')->references('id')->on('website_sites')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_tags');
    }
};
