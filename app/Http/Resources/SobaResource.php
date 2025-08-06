<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SobaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'naziv' => $this->naziv,
            'opis' => $this->opis,
            'je_javna' => $this->je_javna,
            'maksimalan_broj_clanova' => $this->maksimalan_broj_clanova,
            'broj_clanova' => $this->when(isset($this->clanovi_count), $this->clanovi_count),
            'broj_poruka' => $this->when(isset($this->poruke_count), $this->poruke_count),
            'kreirana' => $this->created_at->format('d.m.Y H:i'),
            'azurirana' => $this->updated_at->format('d.m.Y H:i'),
            'clanovi' => $this->when($request->routeIs('*.show'), $this->clanovi),
            'poruke' => $this->when($request->routeIs('*.show'), $this->poruke),
        ];
    }
}
