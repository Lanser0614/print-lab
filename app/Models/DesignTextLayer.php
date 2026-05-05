<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignTextLayer extends Model
{
    protected $fillable = [
        'design_id',
        'layer_id',
        'text',
        'font_family',
        'font_size',
        'color',
        'x',
        'y',
        'scale',
        'rotation',
    ];

    public function design(): BelongsTo
    {
        return $this->belongsTo(Design::class);
    }
}
