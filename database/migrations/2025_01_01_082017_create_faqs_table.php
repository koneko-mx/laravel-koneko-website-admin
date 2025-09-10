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
        Schema::create('faqs', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('category_id')->nullable()->index();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();

            // Auditoria
            $table->unsignedMediumInteger('created_by')->nullable()->index();
            $table->unsignedMediumInteger('updated_by')->nullable()->index();

            $table->timestamps();

            // Indices
            $table->index(['category_id', 'is_active']);

            // Relaciones
            $table->foreign('category_id')->references('id')->on('faq_categories')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
