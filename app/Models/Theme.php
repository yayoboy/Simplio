<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_global',
        'created_by',
        'design_tokens',
        'preview_image',
        'is_active',
    ];

    protected $casts = [
        'design_tokens' => 'array',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($theme) {
            if (empty($theme->slug)) {
                $theme->slug = Str::slug($theme->name);
            }
        });
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    // Scopes
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    public function getDesignToken(string $path, $default = null)
    {
        return data_get($this->design_tokens, $path, $default);
    }

    public function setDesignToken(string $path, $value): void
    {
        $tokens = $this->design_tokens ?? [];
        data_set($tokens, $path, $value);
        $this->design_tokens = $tokens;
    }

    public function generateCss(): string
    {
        $css = ':root {';

        foreach ($this->design_tokens as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $css .= "\n  --{$key}-{$subKey}: {$subValue};";
                }
            } else {
                $css .= "\n  --{$key}: {$value};";
            }
        }

        $css .= "\n}";

        return $css;
    }
}
