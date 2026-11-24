<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['title', 'body', 'color'];

    /** Pilihan warna catatan: kunci => label */
    public const COLORS = [
        'krem'   => 'Krem',
        'hijau'  => 'Hijau',
        'kuning' => 'Kuning',
        'biru'   => 'Biru',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}