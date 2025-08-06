<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClanSobe extends Model
{
    protected $fillable = [
        'korisnik_id',
        'soba_id',
        'uloga',
        'je_aktivan',
        'poslednja_aktivnost'
    ];

    protected $casts = [
        'je_aktivan' => 'boolean',
        'poslednja_aktivnost' => 'datetime'
    ];

    /**
     * Odnos sa korisnikom
     */
    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }

    /**
     * Odnos sa sobom
     */
    public function soba(): BelongsTo
    {
        return $this->belongsTo(Soba::class, 'soba_id');
    }
}
