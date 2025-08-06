<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registracije korisnika
     */
    public function test_korisnik_se_moze_registrovati(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test Korisnik',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ],
                        'token'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test Korisnik',
            'email' => 'test@example.com'
        ]);
    }

    /**
     * Test registracije sa neispravnim podacima
     */
    public function test_registracija_sa_neispravnim_podacima(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'neispravan-email',
            'password' => 'kratka',
            'password_confirmation' => 'razlicita'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test prijave korisnika
     */
    public function test_korisnik_se_moze_prijaviti(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ],
                        'token'
                    ]
                ]);
    }

    /**
     * Test prijave sa neispravnim podacima
     */
    public function test_prijava_sa_neispravnim_podacima(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'pogresna-lozinka'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Neispravni podaci za prijavu'
                ]);
    }

    /**
     * Test odjave korisnika
     */
    public function test_korisnik_se_moze_odjaviti(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Uspešna odjava'
                ]);
    }

    /**
     * Test dohvatanja informacija o trenutnom korisniku
     */
    public function test_dohvatanje_informacija_o_korisniku(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/me');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
    }
}
