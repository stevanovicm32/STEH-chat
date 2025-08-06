<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KorisnikOnlineStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $korisnik;
    public $jeOnline;

    /**
     * Create a new event instance.
     */
    public function __construct(User $korisnik, bool $jeOnline)
    {
        $this->korisnik = $korisnik;
        $this->jeOnline = $jeOnline;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('online-status'),
        ];
    }

    /**
     * Podaci koji će biti poslati kroz WebSocket
     */
    public function broadcastWith(): array
    {
        return [
            'korisnik_id' => $this->korisnik->id,
            'name' => $this->korisnik->name,
            'je_online' => $this->jeOnline,
            'poslednja_aktivnost' => $this->korisnik->poslednja_aktivnost?->format('d.m.Y H:i')
        ];
    }

    /**
     * Naziv eventa
     */
    public function broadcastAs(): string
    {
        return 'korisnik-status';
    }
}
