<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_add_annonce_to_favorites()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create();

        $this->actingAs($user)->post(route('favorites.toggle', $annonce), [])
            ->assertRedirect();

        $this->assertTrue($user->favorites()->where('annonce_id', $annonce->id)->exists());
    }

    #[Test]
    public function authenticated_user_can_remove_annonce_from_favorites()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create();

        $user->favorites()->attach($annonce->id);

        $this->actingAs($user)->post(route('favorites.toggle', $annonce), [])
            ->assertRedirect();

        $this->assertFalse($user->favorites()->where('annonce_id', $annonce->id)->exists());
    }

    #[Test]
    public function user_can_view_favorites_page()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonces = Annonce::factory()->count(3)->create();

        $user->favorites()->attach($annonces->pluck('id'));

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk();
        foreach ($annonces as $annonce) {
            $response->assertSee($annonce->titre);
        }
    }

    #[Test]
    public function favorites_page_shows_empty_state_when_no_favorites()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('Aucun favori pour le moment');
    }

    #[Test]
    public function non_owner_cannot_toggle_own_annonce_as_favorite()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $user->id]);

        $stranger = User::factory()->create(['role' => 'user']);

        $this->actingAs($stranger)->post(route('favorites.toggle', $annonce), [])
            ->assertRedirect();

        $this->assertFalse($stranger->favorites()->where('annonce_id', $annonce->id)->exists());
    }

    #[Test]
    public function unauthenticated_user_cannot_access_favorites()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function favorites_are_paginated()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonces = Annonce::factory()->count(15)->create();

        $user->favorites()->attach($annonces->pluck('id'));

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertViewHas('favorites');
    }

    #[Test]
    public function favorite_count_is_accurate()
    {
        $user = User::factory()->create(['role' => 'user']);
        $annonces = Annonce::factory()->count(5)->create();

        $user->favorites()->attach($annonces->pluck('id'));

        $this->assertEquals(5, $user->favorites()->count());
    }
}
