<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $fillable = [
        'level_id',
        'title',
        'content',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}