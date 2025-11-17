<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'name',
        'content',
        'properties',
        'position',
        'order',
        'is_visible',
        'parent_id',
    ];

    protected $casts = [
        'content' => 'array',
        'properties' => 'array',
        'position' => 'array',
        'is_visible' => 'boolean',
    ];

    // Block types
    public const TYPE_TEXT = 'text';
    public const TYPE_HEADING = 'heading';
    public const TYPE_IMAGE = 'image';
    public const TYPE_GALLERY = 'gallery';
    public const TYPE_VIDEO = 'video';
    public const TYPE_HTML = 'html';
    public const TYPE_BUTTON = 'button';
    public const TYPE_DIVIDER = 'divider';
    public const TYPE_SPACER = 'spacer';
    public const TYPE_CONTAINER = 'container';
    public const TYPE_COLUMNS = 'columns';

    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_HEADING,
            self::TYPE_IMAGE,
            self::TYPE_GALLERY,
            self::TYPE_VIDEO,
            self::TYPE_HTML,
            self::TYPE_BUTTON,
            self::TYPE_DIVIDER,
            self::TYPE_SPACER,
            self::TYPE_CONTAINER,
            self::TYPE_COLUMNS,
        ];
    }

    // Relationships
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(PageBlock::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PageBlock::class, 'parent_id')->ordered();
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper methods
    public function show(): bool
    {
        $this->is_visible = true;
        return $this->save();
    }

    public function hide(): bool
    {
        $this->is_visible = false;
        return $this->save();
    }

    public function updatePosition(array $position): bool
    {
        $this->position = array_merge($this->position ?? [], $position);
        return $this->save();
    }

    public function updateContent(array $content): bool
    {
        $this->content = array_merge($this->content ?? [], $content);
        return $this->save();
    }

    public function duplicate(): self
    {
        $newBlock = $this->replicate();
        $newBlock->name = ($this->name ?? '') . ' (Copy)';
        $newBlock->save();

        return $newBlock;
    }
}
