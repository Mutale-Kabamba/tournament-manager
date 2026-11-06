<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_tournament_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Play it Forward');
        $response->assertSee('Boys Tournament');
        $response->assertSee('Girls Tournament');
    }

    public function test_tournament_api_returns_state(): void
    {
        $response = $this->getJson('/api/tournament/state');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'title',
                'state' => [
                    'boysTeams',
                    'girlsTeams',
                    'boysMatches',
                    'girlsMatches',
                    'boysKnockout',
                    'girlsKnockout',
                ],
            ]);
    }

    public function test_verify_pin_fails_with_wrong_pin(): void
    {
        $response = $this->postJson('/api/tournament/verify-pin', [
            'pin' => '0000',
        ]);

        $response->assertStatus(401);
    }

    public function test_verify_pin_succeeds_with_correct_pin(): void
    {
        $response = $this->postJson('/api/tournament/verify-pin', [
            'pin' => '1234',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_admin_can_update_state(): void
    {
        $stateResponse = $this->getJson('/api/tournament/state');
        $state = $stateResponse->json('state');

        // Update score of first match
        $state['boysMatches'][0]['homeScore'] = 3;
        $state['boysMatches'][0]['awayScore'] = 1;
        $state['boysMatches'][0]['status'] = 'completed';

        $updateResponse = $this->postJson('/api/tournament/state', [
            'pin' => '1234',
            'state' => $state,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $checkResponse = $this->getJson('/api/tournament/state');
        $this->assertEquals(3, $checkResponse->json('state.boysMatches.0.homeScore'));
        $this->assertEquals(1, $checkResponse->json('state.boysMatches.0.awayScore'));
        $this->assertEquals('completed', $checkResponse->json('state.boysMatches.0.status'));
    }

    public function test_admin_can_change_pin(): void
    {
        $response = $this->postJson('/api/tournament/change-pin', [
            'current_pin' => '1234',
            'new_pin' => '5678',
        ]);

        $response->assertStatus(200);

        // Old PIN no longer works
        $failResponse = $this->postJson('/api/tournament/verify-pin', ['pin' => '1234']);
        $failResponse->assertStatus(401);

        // New PIN works
        $successResponse = $this->postJson('/api/tournament/verify-pin', ['pin' => '5678']);
        $successResponse->assertStatus(200);
    }
}

