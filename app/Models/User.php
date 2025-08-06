<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Odnos sa porukama koje je korisnik poslao
     */
    public function poruke(): HasMany
    {
        return $this->hasMany(Poruka::class, 'korisnik_id');
    }

    /**
     * Odnos sa članovima soba
     */
    public function clanoviSoba(): HasMany
    {
        return $this->hasMany(ClanSobe::class, 'korisnik_id');
    }

    /**
     * Odnos sa sobama kroz članove soba
     */
    public function sobe(): BelongsToMany
    {
        return $this->belongsToMany(Soba::class, 'clan_sobes')
                    ->withPivot('uloga', 'je_aktivan', 'poslednja_aktivnost')
                    ->withTimestamps();
    }
}
