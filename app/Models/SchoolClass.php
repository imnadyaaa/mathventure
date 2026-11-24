<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
    ];

    public function levels(): HasMany
    {
        return $this->hasMany(Level::class, 'class_id');
    }
}