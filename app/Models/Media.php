<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'path',
        'disk',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'alt_text',
        'caption',
        'metadata',
        'variants',
    ];

    protected $casts = [
        'metadata' => 'array',
        'variants' => 'array',
    ];

    // Relationships
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereNotIn('mime_type', function ($q) {
            $q->select('mime_type')
                ->from('media')
                ->where('mime_type', 'like', 'image/%')
                ->orWhere('mime_type', 'like', 'video/%');
        });
    }

    // Helper methods
    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getVariantUrl(string $variant): ?string
    {
        if (!isset($this->variants[$variant])) {
            return null;
        }

        return Storage::disk($this->disk)->url($this->variants[$variant]['path']);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function getSizeForHumans(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensions(): ?array
    {
        if ($this->width && $this->height) {
            return [
                'width' => $this->width,
                'height' => $this->height,
            ];
        }

        return null;
    }

    public function addVariant(string $name, array $variantData): bool
    {
        $variants = $this->variants ?? [];
        $variants[$name] = $variantData;
        $this->variants = $variants;

        return $this->save();
    }

    public function delete()
    {
        // Delete file from storage
        if (Storage::disk($this->disk)->exists($this->path)) {
            Storage::disk($this->disk)->delete($this->path);
        }

        // Delete variants
        if ($this->variants) {
            foreach ($this->variants as $variant) {
                if (isset($variant['path']) && Storage::disk($this->disk)->exists($variant['path'])) {
                    Storage::disk($this->disk)->delete($variant['path']);
                }
            }
        }

        return parent::delete();
    }
}
