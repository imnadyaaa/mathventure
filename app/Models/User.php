<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = ['name', 'username', 'email', 'password'];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'muted_until' => 'datetime',
            'is_moderator' => 'boolean',
        ];
    }

     public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(\App\Models\Note::class);
    }

     public const XP_PER_LEVEL = 100;
    public const MAX_LEVEL = 5;

    public function isMaxLevel(): bool
    {
        return $this->level >= self::MAX_LEVEL;
    }

    public function addXp(int $amount): void
    {
        $this->xp += $amount;
        $this->level = min(self::MAX_LEVEL, intdiv($this->xp, self::XP_PER_LEVEL) + 1);
        $this->save();
    }

    public function xpInLevel(): int
    {
        return $this->isMaxLevel() ? self::XP_PER_LEVEL : $this->xp % self::XP_PER_LEVEL;
    }

    public function xpProgressPercent(): int
    {
        return $this->isMaxLevel() ? 100 : (int) round($this->xpInLevel() / self::XP_PER_LEVEL * 100);
    }

    public function avatarUrl(): string
    {
    $kunci = $this->avatar ?? 'rubah';
    if (!array_key_exists($kunci, config('avatars', []))) {
        $kunci = 'rubah';
    }
    return asset('avatar/' . $kunci . '.svg');
    }

    
}
