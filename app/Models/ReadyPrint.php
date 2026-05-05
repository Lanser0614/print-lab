<?php

namespace App\Models;

use Database\Factories\ReadyPrintFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ReadyPrint extends Model
{
    /** @use HasFactory<ReadyPrintFactory> */
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'slug', 'image_path', 'is_active'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        return str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : Storage::url($this->image_path);
    }
}
