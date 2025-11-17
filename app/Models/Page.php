<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'description',
        'layout',
        'settings',
        'meta_title',
        'meta_description',
        'meta',
        'is_home',
        'is_published',
        'order',
        'template',
        'published_at',
    ];

    protected $casts = [
        'layout' => 'array',
        'settings' => 'array',
        'meta' => 'array',
        'is_home' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }

            // Ensure only one home page per site
            if ($page->is_home) {
                static::where('site_id', $page->site_id)
                    ->where('is_home', true)
                    ->update(['is_home' => false]);
            }
        });

        static::updating(function ($page) {
            // Ensure only one home page per site
            if ($page->is_home && $page->isDirty('is_home')) {
                static::where('site_id', $page->site_id)
                    ->where('id', '!=', $page->id)
                    ->where('is_home', true)
                    ->update(['is_home' => false]);
            }
        });
    }

    // Relationships
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('order');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeHome($query)
    {
        return $query->where('is_home', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper methods
    public function publish(): bool
    {
        $this->is_published = true;
        $this->published_at = now();
        return $this->save();
    }

    public function unpublish(): bool
    {
        $this->is_published = false;
        return $this->save();
    }

    public function setAsHome(): bool
    {
        $this->is_home = true;
        return $this->save();
    }

    public function duplicate(): self
    {
        $newPage = $this->replicate();
        $newPage->title = $this->title . ' (Copy)';
        $newPage->slug = Str::slug($newPage->title);
        $newPage->is_published = false;
        $newPage->is_home = false;
        $newPage->save();

        // Duplicate blocks
        foreach ($this->blocks as $block) {
            $newBlock = $block->replicate();
            $newBlock->page_id = $newPage->id;
            $newBlock->save();
        }

        return $newPage;
    }
}
