<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poruka extends Model
{
    protected $fillable = [
        'sadrzaj',
        'korisnik_id',
        'soba_id',
        'tip_poruke',
        'fajl_putanja',
        'je_procitana'
    ];

    protected $casts = [
        'je_procitana' => 'boolean'
    ];

    /**
     * Odnos sa korisnikom koji je poslao poruku
     */
    public function korisnik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korisnik_id');
    }

    /**
     * Odnos sa sobom u kojoj je poruka poslata
     */
    public function soba(): BelongsTo
    {
        return $this->belongsTo(Soba::class, 'soba_id');
    }
}
