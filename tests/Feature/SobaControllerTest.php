<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Soba;
use App\Models\ClanSobe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SobaControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test prikaza javnih soba
     */
    public function test_prikaz_javnih_soba(): void
    {
        Soba::factory()->create(['je_javna' => true]);
        Soba::factory()->create(['je_javna' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/sobe');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'naziv',
                            'opis',
                            'je_javna',
                            'maksimalan_broj_clanova'
                        ]
                    ]
                ]);

        $this->assertEquals(1, count($response->json('data')));
    }

    /**
     * Test kreiranja nove sobe
     */
    public function test_kreiranje_nove_sobe(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/sobe', [
            'naziv' => 'Test Soba',
            'opis' => 'Opis test sobe',
            'je_javna' => true,
            'maksimalan_broj_clanova' => 50
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Soba uspešno kreirana'
                ]);

        $this->assertDatabaseHas('sobas', [
            'naziv' => 'Test Soba',
            'opis' => 'Opis test sobe'
        ]);

        // Provera da li je korisnik automatski dodan kao admin
        $this->assertDatabaseHas('clan_sobes', [
            'korisnik_id' => $this->user->id,
            'uloga' => 'admin'
        ]);
    }

    /**
     * Test prikaza određene sobe
     */
    public function test_prikaz_odredene_sobe(): void
    {
        $soba = Soba::factory()->create(['je_javna' => true]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/sobe/' . $soba->id);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $soba->id,
                        'naziv' => $soba->naziv
                    ]
                ]);
    }

    /**
     * Test pristupa privatnoj sobi bez dozvole
     */
    public function test_pristup_privatnoj_sobi_bez_dozvole(): void
    {
        $soba = Soba::factory()->create(['je_javna' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/sobe/' . $soba->id);

        $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Nemate pristup ovoj sobi'
                ]);
    }

    /**
     * Test pridruživanja sobi
     */
    public function test_pridruzivanje_sobi(): void
    {
        $soba = Soba::factory()->create(['je_javna' => true]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/sobe/' . $soba->id . '/pridruzi-se');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Uspešno ste se pridružili sobi'
                ]);

        $this->assertDatabaseHas('clan_sobes', [
            'korisnik_id' => $this->user->id,
            'soba_id' => $soba->id,
            'uloga' => 'clan'
        ]);
    }

    /**
     * Test napuštanja sobe
     */
    public function test_napustanje_sobe(): void
    {
        $soba = Soba::factory()->create();
        ClanSobe::create([
            'korisnik_id' => $this->user->id,
            'soba_id' => $soba->id,
            'uloga' => 'clan'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson('/api/sobe/' . $soba->id . '/napusti');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Uspešno ste napustili sobu'
                ]);

        $this->assertDatabaseMissing('clan_sobes', [
            'korisnik_id' => $this->user->id,
            'soba_id' => $soba->id
        ]);
    }
}
