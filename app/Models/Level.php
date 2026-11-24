<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = [
        'class_id',
        'level_number',
        'name',
        'minimum_score',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class);
    }
}