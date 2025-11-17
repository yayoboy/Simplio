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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('layout')->nullable(); // Grid configuration, responsive settings
            $table->json('settings')->nullable(); // Page-specific settings
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta')->nullable(); // Additional meta tags
            $table->boolean('is_home')->default(false);
            $table->boolean('is_published')->default(false);
            $table->integer('order')->default(0);
            $table->string('template')->nullable(); // Template name for rendering
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Ensure slug is unique per site
            $table->unique(['site_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
