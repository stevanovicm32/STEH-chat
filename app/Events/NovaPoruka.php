<?php

namespace App\Events;

use App\Models\Poruka;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NovaPoruka implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poruka;

    /**
     * Create a new event instance.
     */
    public function __construct(Poruka $poruka)
    {
        $this->poruka = $poruka;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('soba.' . $this->poruka->soba_id),
        ];
    }

    /**
     * Podaci koji će biti poslati kroz WebSocket
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->poruka->id,
            'sadrzaj' => $this->poruka->sadrzaj,
            'tip_poruke' => $this->poruka->tip_poruke,
            'je_procitana' => $this->poruka->je_procitana,
            'kreirana' => $this->poruka->created_at->format('d.m.Y H:i'),
            'korisnik' => [
                'id' => $this->poruka->korisnik->id,
                'name' => $this->poruka->korisnik->name,
                'email' => $this->poruka->korisnik->email
            ],
            'soba_id' => $this->poruka->soba_id
        ];
    }

    /**
     * Naziv eventa
     */
    public function broadcastAs(): string
    {
        return 'nova-poruka';
    }
}
