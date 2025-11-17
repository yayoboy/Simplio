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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('filename'); // Generated unique filename
            $table->string('original_filename'); // Original uploaded filename
            $table->string('path'); // File path
            $table->string('disk')->default('local'); // Storage disk: local, s3, etc.
            $table->string('mime_type');
            $table->string('extension')->nullable();
            $table->unsignedBigInteger('size'); // File size in bytes
            $table->integer('width')->nullable(); // Image width
            $table->integer('height')->nullable(); // Image height
            $table->string('alt_text')->nullable(); // Alt text for accessibility
            $table->text('caption')->nullable();
            $table->json('metadata')->nullable(); // EXIF data, variants (thumbnails, webp, etc.), focal point
            $table->json('variants')->nullable(); // Different sizes and formats generated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
