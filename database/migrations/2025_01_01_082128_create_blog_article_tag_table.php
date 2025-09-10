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
        Schema::create('blog_article_tag', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('blog_article_id')->index();
            $table->unsignedSmallInteger('blog_tag_id')->index();

            // Auditoria
            $table->timestamps();

            // Indices
            $table->index(['blog_article_id', 'blog_tag_id']);

            // Relaciones
            $table->foreign('blog_article_id')->references('id')->on('blog_articles')->cascadeOnDelete();
            $table->foreign('blog_tag_id')->references('id')->on('blog_tags')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_article_tag');
    }
};
