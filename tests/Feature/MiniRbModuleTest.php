<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Annonce;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MiniRbModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register_with_a_role()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'hote',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'hote',
        ]);
    }

    /** @test */
    public function a_user_can_create_an_annonce()
    {
        $user = User::factory()->create(['role' => 'hote']);
        $this->actingAs($user);

        $response = $this->post('/annonces', [
            'titre' => 'Bel appartement',
            'description' => 'Superbe vue sur mer',
            'adresse' => '123 Rue de la Mer',
            'ville' => 'Casablanca',
            'prix_par_nuit' => 500,
            'nombre_de_chambres' => 2,
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('annonces', [
            'titre' => 'Bel appartement',
            'ville' => 'Casablanca',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function only_owner_or_admin_can_update_an_annonce()
    {
        $owner = User::factory()->create(['role' => 'hote']);
        $otherUser = User::factory()->create(['role' => 'voyageur']);
        $admin = User::factory()->create(['role' => 'admin']);
        
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        // Un autre utilisateur ne peut pas modifier
        $this->actingAs($otherUser);
        $this->get("/annonces/{$annonce->id}/edit")->assertStatus(403);
        $this->put("/annonces/{$annonce->id}", ['titre' => 'Titre modifié'])->assertStatus(403);

        // Le propriétaire peut modifier
        $this->actingAs($owner);
        $this->get("/annonces/{$annonce->id}/edit")->assertStatus(200);
        $this->put("/annonces/{$annonce->id}", [
            'titre' => 'Titre par Proprio',
            'description' => $annonce->description,
            'adresse' => $annonce->adresse,
            'ville' => $annonce->ville,
            'prix_par_nuit' => 600,
            'nombre_de_chambres' => 3,
        ])->assertRedirect("/annonces/{$annonce->id}");

        // L'admin peut modifier
        $this->actingAs($admin);
        $this->get("/annonces/{$annonce->id}/edit")->assertStatus(200);
    }

    /** @test */
    public function search_and_filters_work_correctly()
    {
        Annonce::factory()->create(['ville' => 'Paris', 'prix_par_nuit' => 100]);
        Annonce::factory()->create(['ville' => 'Lyon', 'prix_par_nuit' => 200]);
        Annonce::factory()->create(['ville' => 'Paris', 'prix_par_nuit' => 300]);

        // Test filtre par ville
        $response = $this->get('/?ville=Paris');
        $response->assertStatus(200);
        $response->assertSee('Paris');
        $response->assertDontSee('Lyon');

        // Test filtre par prix max
        $response = $this->get('/?prix_max=150');
        $response->assertSee('100$');
        $response->assertDontSee('200$');
        $response->assertDontSee('300$');
    }
}
