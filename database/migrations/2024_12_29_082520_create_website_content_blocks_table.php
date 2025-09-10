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
        Schema::create('website_content_blocks', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->string('slug', 64)->nullable();
            $table->string('description')->nullable();
            $table->json('keywords')->nullable();

            $table->json('data')->nullable();
            $table->json('settings')->nullable();

            $table->boolean('enable_cache')->default(true);
            $table->unsignedMediumInteger('cache_ttl')->default(43800); // 12 hours

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_content_blocks');
    }
};
