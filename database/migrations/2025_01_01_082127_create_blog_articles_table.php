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
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('category_id')->index();

            $table->string('title')->index();
            $table->string('slug')->index();

            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->json('metadata')->nullable();

            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();

            $table->unsignedMediumInteger('created_by')->index();
            $table->unsignedMediumInteger('updated_by')->index();

            // Auditoria
            $table->timestamps();

            // Indices
            $table->unique(['slug', 'category_id']);
            $table->index(['title', 'is_published']);

            // Relaciones
            $table->foreign('category_id')->references('id')->on('blog_categories')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
