<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'domain',
        'theme_id',
        'description',
        'settings',
        'meta',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'meta' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($site) {
            if (empty($site->slug)) {
                $site->slug = Str::slug($site->name);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOwned($query, $userId)
    {
        return $query->where('user_id', $userId);
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

    public function getHomePage(): ?Page
    {
        return $this->pages()->where('is_home', true)->first();
    }
}
