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
        Schema::create('website_content_versions', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedMediumInteger('website_content_blocks_id')->index();
            $table->string('version_label', 64)->index();
            $table->longText('content');
            $table->boolean('is_current')->default(false);
            $table->json('metadata')->nullable();

            // Auditoria
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();

            $table->timestamps();

            // Indices
            $table->index(['website_content_blocks_id', 'version_label'], 'website_content_versions_blocks_id_version_label_index');
            $table->index(['website_content_blocks_id', 'is_current'], 'website_content_versions_blocks_id_is_current_index');

            // Relaciones
            $table->foreign('website_content_blocks_id')->references('id')->on('website_content_blocks')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_content_versions');
    }
};
