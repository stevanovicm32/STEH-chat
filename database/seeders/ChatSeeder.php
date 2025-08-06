<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Soba;
use App\Models\Poruka;
use App\Models\ClanSobe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kreiranje test korisnika
        $korisnik1 = User::create([
            'name' => 'Marko Stevanović',
            'email' => 'marko@example.com',
            'password' => Hash::make('password123'),
            'je_online' => true
        ]);

        $korisnik2 = User::create([
            'name' => 'Ana Petrović',
            'email' => 'ana@example.com',
            'password' => Hash::make('password123'),
            'je_online' => false
        ]);

        $korisnik3 = User::create([
            'name' => 'Petar Jovanović',
            'email' => 'petar@example.com',
            'password' => Hash::make('password123'),
            'je_online' => true
        ]);

        // Kreiranje test soba
        $soba1 = Soba::create([
            'naziv' => 'Opšta diskusija',
            'opis' => 'Opšta soba za razgovor o svemu i svačemu',
            'je_javna' => true,
            'maksimalan_broj_clanova' => 100
        ]);

        $soba2 = Soba::create([
            'naziv' => 'Programiranje',
            'opis' => 'Soba za diskusiju o programiranju i tehnologiji',
            'je_javna' => true,
            'maksimalan_broj_clanova' => 50
        ]);

        $soba3 = Soba::create([
            'naziv' => 'Privatna soba',
            'opis' => 'Privatna soba za testiranje',
            'je_javna' => false,
            'maksimalan_broj_clanova' => 10
        ]);

        // Dodavanje korisnika u sobe
        ClanSobe::create([
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba1->id,
            'uloga' => 'admin'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik2->id,
            'soba_id' => $soba1->id,
            'uloga' => 'clan'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik3->id,
            'soba_id' => $soba1->id,
            'uloga' => 'clan'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba2->id,
            'uloga' => 'admin'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik2->id,
            'soba_id' => $soba2->id,
            'uloga' => 'moderator'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba3->id,
            'uloga' => 'admin'
        ]);

        ClanSobe::create([
            'korisnik_id' => $korisnik2->id,
            'soba_id' => $soba3->id,
            'uloga' => 'clan'
        ]);

        // Kreiranje test poruka
        Poruka::create([
            'sadrzaj' => 'Zdravo svima! Dobrodošli u opštu diskusiju.',
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba1->id,
            'tip_poruke' => 'tekst'
        ]);

        Poruka::create([
            'sadrzaj' => 'Hvala! Drago mi je što sam ovde.',
            'korisnik_id' => $korisnik2->id,
            'soba_id' => $soba1->id,
            'tip_poruke' => 'tekst'
        ]);

        Poruka::create([
            'sadrzaj' => 'Kako ide danas?',
            'korisnik_id' => $korisnik3->id,
            'soba_id' => $soba1->id,
            'tip_poruke' => 'tekst'
        ]);

        Poruka::create([
            'sadrzaj' => 'Da li neko zna Laravel?',
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba2->id,
            'tip_poruke' => 'tekst'
        ]);

        Poruka::create([
            'sadrzaj' => 'Ja radim sa Laravel-om već 2 godine!',
            'korisnik_id' => $korisnik2->id,
            'soba_id' => $soba2->id,
            'tip_poruke' => 'tekst'
        ]);

        Poruka::create([
            'sadrzaj' => 'Ovo je privatna poruka.',
            'korisnik_id' => $korisnik1->id,
            'soba_id' => $soba3->id,
            'tip_poruke' => 'tekst'
        ]);
    }
}
