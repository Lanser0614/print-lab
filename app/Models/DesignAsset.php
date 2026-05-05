<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignAsset extends Model
{
    protected $fillable = [
        'design_id',
        'type',
        'original_file_path',
        'file_name',
        'mime_type',
        'size_bytes',
        'metadata',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }
}
