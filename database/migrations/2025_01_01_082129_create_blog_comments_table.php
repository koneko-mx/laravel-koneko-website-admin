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
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedSmallInteger('blog_article_id')->index();

            $table->unsignedMediumInteger('author_id')->nullable()->index();
            $table->string('author_name')->index();
            $table->string('author_email')->index();
            $table->text('comment');

            $table->boolean('is_approved')->default(false)->index();

            // Auditoria
            $table->unsignedMediumInteger('updated_by')->index();
            $table->timestamps();

            // Indices
            $table->index(['blog_article_id', 'is_approved']);

            // Relaciones
            $table->foreign('blog_article_id')->references('id')->on('blog_articles')->restrictOnDelete();
            $table->foreign('author_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
