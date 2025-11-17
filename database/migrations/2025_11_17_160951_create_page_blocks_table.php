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
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, heading, image, gallery, video, html, button, divider, etc.
            $table->string('name')->nullable(); // User-friendly name for the block
            $table->json('content'); // Block content (text, image URL, HTML, etc.)
            $table->json('properties')->nullable(); // Styles, alignment, padding, margin, colors, etc.
            $table->json('position')->nullable(); // Grid position: {row, column, width, height, responsive settings}
            $table->integer('order')->default(0); // Order within the page
            $table->boolean('is_visible')->default(true);
            $table->string('parent_id')->nullable(); // For nested blocks (containers)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};
