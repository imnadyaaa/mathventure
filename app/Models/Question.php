<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'level_id',
        'question',
        'type',
        'answer',
        'options',
        'points',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'points'  => 'integer',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}