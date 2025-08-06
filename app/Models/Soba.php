<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Soba extends Model
{
    protected $fillable = [
        'naziv',
        'opis',
        'je_javna',
        'maksimalan_broj_clanova'
    ];

    protected $casts = [
        'je_javna' => 'boolean',
        'maksimalan_broj_clanova' => 'integer'
    ];

    /**
     * Odnos sa porukama
     */
    public function poruke(): HasMany
    {
        return $this->hasMany(Poruka::class);
    }

    /**
     * Odnos sa članovima sobe
     */
    public function clanovi(): HasMany
    {
        return $this->hasMany(ClanSobe::class);
    }

    /**
     * Odnos sa korisnicima kroz članove sobe
     */
    public function korisnici(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'clan_sobes')
                    ->withPivot('uloga', 'je_aktivan', 'poslednja_aktivnost')
                    ->withTimestamps();
    }
}
