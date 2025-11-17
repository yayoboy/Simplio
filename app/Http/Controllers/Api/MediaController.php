<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        // Initialize Intervention Image with GD driver
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Display a listing of media for a site.
     */
    public function index(Request $request, Site $site): JsonResponse
    {
        $this->authorize('view', $site);

        $query = $site->media()->with('uploader:id,name,email');

        // Filter by type
        if ($request->has('type')) {
            $type = $request->input('type');
            if ($type === 'images') {
                $query->images();
            } elseif ($type === 'videos') {
                $query->videos();
            } elseif ($type === 'documents') {
                $query->documents();
            }
        }

        // Search by filename
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginate or get all
        if ($request->has('per_page')) {
            $media = $query->paginate($request->input('per_page', 20));
        } else {
            $media = $query->get();
        }

        // Add URLs to each media item
        $media->transform(function ($item) {
            $item->url = $item->getUrl();
            if ($item->variants) {
                foreach ($item->variants as $variant => $data) {
                    $item->variants[$variant]['url'] = $item->getVariantUrl($variant);
                }
            }
            return $item;
        });

        return response()->json([
            'data' => $media,
        ]);
    }

    /**
     * Store a newly uploaded media file.
     */
    public function store(Request $request, Site $site): JsonResponse
    {
        $this->authorize('update', $site);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;

        // Storage path: media/{site_id}/{year}/{month}
        $storagePath = 'media/' . $site->id . '/' . date('Y') . '/' . date('m');
        $path = $storagePath . '/' . $filename;

        // Determine disk from config (default to public for easy access)
        $disk = config('filesystems.default', 'public');

        // Store the file
        $file->storeAs($storagePath, $filename, $disk);

        // Initialize media data
        $mediaData = [
            'site_id' => $site->id,
            'uploaded_by' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
        ];

        // Process image: get dimensions and create variants
        if (str_starts_with($mimeType, 'image/')) {
            try {
                $fullPath = Storage::disk($disk)->path($path);
                $image = $this->imageManager->read($fullPath);

                // Get dimensions
                $mediaData['width'] = $image->width();
                $mediaData['height'] = $image->height();

                // Create variants (thumbnail, medium, large)
                $variants = $this->createImageVariants($image, $storagePath, $filename, $disk);
                $mediaData['variants'] = $variants;

                // Extract EXIF data if available
                $mediaData['metadata'] = $this->extractMetadata($fullPath);
            } catch (\Exception $e) {
                // If image processing fails, continue without variants
                \Log::warning('Image processing failed: ' . $e->getMessage());
            }
        }

        // Create media record
        $media = Media::create($mediaData);

        // Add URL for response
        $media->url = $media->getUrl();
        if ($media->variants) {
            foreach ($media->variants as $variant => $data) {
                $media->variants[$variant]['url'] = $media->getVariantUrl($variant);
            }
        }

        return response()->json([
            'data' => $media,
            'message' => 'Media uploaded successfully',
        ], 201);
    }

    /**
     * Display the specified media.
     */
    public function show(Site $site, Media $media): JsonResponse
    {
        $this->authorize('view', $site);

        if ($media->site_id !== $site->id) {
            return response()->json(['error' => 'Media not found'], 404);
        }

        $media->url = $media->getUrl();
        if ($media->variants) {
            foreach ($media->variants as $variant => $data) {
                $media->variants[$variant]['url'] = $media->getVariantUrl($variant);
            }
        }

        return response()->json([
            'data' => $media,
        ]);
    }

    /**
     * Update the specified media metadata.
     */
    public function update(Request $request, Site $site, Media $media): JsonResponse
    {
        $this->authorize('update', $site);

        if ($media->site_id !== $site->id) {
            return response()->json(['error' => 'Media not found'], 404);
        }

        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'original_filename' => 'nullable|string|max:255',
        ]);

        $media->update($request->only(['alt_text', 'caption', 'original_filename']));

        $media->url = $media->getUrl();

        return response()->json([
            'data' => $media,
            'message' => 'Media updated successfully',
        ]);
    }

    /**
     * Remove the specified media.
     */
    public function destroy(Site $site, Media $media): JsonResponse
    {
        $this->authorize('update', $site);

        if ($media->site_id !== $site->id) {
            return response()->json(['error' => 'Media not found'], 404);
        }

        $media->delete(); // This will also delete the file from storage

        return response()->json([
            'message' => 'Media deleted successfully',
        ]);
    }

    /**
     * Regenerate image variants for a media item.
     */
    public function regenerateVariants(Media $media): JsonResponse
    {
        if (!$media->isImage()) {
            return response()->json(['error' => 'Only images can have variants regenerated'], 400);
        }

        try {
            $fullPath = Storage::disk($media->disk)->path($media->path);
            $image = $this->imageManager->read($fullPath);

            // Delete old variants
            if ($media->variants) {
                foreach ($media->variants as $variant) {
                    if (isset($variant['path']) && Storage::disk($media->disk)->exists($variant['path'])) {
                        Storage::disk($media->disk)->delete($variant['path']);
                    }
                }
            }

            // Create new variants
            $storagePath = dirname($media->path);
            $filename = basename($media->path);
            $variants = $this->createImageVariants($image, $storagePath, $filename, $media->disk);
            $media->variants = $variants;
            $media->save();

            $media->url = $media->getUrl();
            if ($media->variants) {
                foreach ($media->variants as $variant => $data) {
                    $media->variants[$variant]['url'] = $media->getVariantUrl($variant);
                }
            }

            return response()->json([
                'data' => $media,
                'message' => 'Variants regenerated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to regenerate variants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create image variants (thumbnails, different sizes).
     */
    protected function createImageVariants($image, string $storagePath, string $filename, string $disk): array
    {
        $variants = [];
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $sizes = [
            'thumbnail' => 150,
            'small' => 300,
            'medium' => 768,
            'large' => 1200,
        ];

        foreach ($sizes as $name => $maxWidth) {
            try {
                // Clone image for resizing
                $variant = clone $image;

                // Resize maintaining aspect ratio
                if ($variant->width() > $maxWidth) {
                    $variant->scale(width: $maxWidth);
                }

                // Generate variant filename
                $variantFilename = "{$baseFilename}_{$name}.{$extension}";
                $variantPath = "{$storagePath}/{$variantFilename}";

                // Save variant
                $fullPath = Storage::disk($disk)->path($variantPath);
                $variant->save($fullPath, quality: 85);

                $variants[$name] = [
                    'path' => $variantPath,
                    'width' => $variant->width(),
                    'height' => $variant->height(),
                    'size' => filesize($fullPath),
                ];
            } catch (\Exception $e) {
                \Log::warning("Failed to create {$name} variant: " . $e->getMessage());
            }
        }

        return $variants;
    }

    /**
     * Extract EXIF metadata from image.
     */
    protected function extractMetadata(string $path): ?array
    {
        try {
            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($path);
                if ($exif) {
                    return [
                        'camera' => $exif['Model'] ?? null,
                        'date_taken' => $exif['DateTimeOriginal'] ?? null,
                        'iso' => $exif['ISOSpeedRatings'] ?? null,
                        'aperture' => $exif['FNumber'] ?? null,
                        'exposure' => $exif['ExposureTime'] ?? null,
                        'focal_length' => $exif['FocalLength'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('EXIF extraction failed: ' . $e->getMessage());
        }

        return null;
    }
}
