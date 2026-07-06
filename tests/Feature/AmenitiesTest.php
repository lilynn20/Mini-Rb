<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AmenitiesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('AmenitySeeder');
    }

    #[Test]
    public function owner_can_add_amenities_to_annonce()
    {
        $owner = User::factory()->create(['role' => 'user']);
        $amenities = Amenity::all()->take(3);

        $this->actingAs($owner)->post('/annonces', [
            'titre' => 'Test',
            'description' => 'Test',
            'adresse' => 'Test',
            'ville' => 'Test',
            'prix_par_nuit' => 100,
            'nombre_de_chambres' => 1,
            'amenities' => $amenities->pluck('id')->toArray(),
        ])->assertRedirect();

        $annonce = Annonce::latest()->first();
        $this->assertEquals(3, $annonce->amenities->count());
    }

    #[Test]
    public function owner_can_update_amenities()
    {
        $owner = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $oldAmenities = Amenity::all()->take(2);
        $annonce->amenities()->sync($oldAmenities->pluck('id'));

        $newAmenities = Amenity::all()->skip(2)->take(2);

        $this->actingAs($owner)->put("/annonces/{$annonce->id}", [
            'titre' => $annonce->titre,
            'description' => $annonce->description,
            'adresse' => $annonce->adresse,
            'ville' => $annonce->ville,
            'prix_par_nuit' => $annonce->prix_par_nuit,
            'nombre_de_chambres' => $annonce->nombre_de_chambres,
            'amenities' => $newAmenities->pluck('id')->toArray(),
        ])->assertRedirect();

        $annonce->refresh();
        $this->assertEquals(2, $annonce->amenities->count());
        $this->assertTrue($annonce->amenities->contains('id', $newAmenities->first()->id));
    }

    #[Test]
    public function amenities_display_on_annonce_show_page()
    {
        $annonce = Annonce::factory()->create();
        $amenities = Amenity::all()->take(3);
        $annonce->amenities()->sync($amenities->pluck('id'));

        $response = $this->get(route('annonces.show', $annonce));

        foreach ($amenities as $amenity) {
            $response->assertSee($amenity->name);
        }
    }

    #[Test]
    public function non_owner_cannot_modify_amenities()
    {
        $owner = User::factory()->create(['role' => 'user']);
        $stranger = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $amenities = Amenity::all()->take(2);

        $this->actingAs($stranger)->put("/annonces/{$annonce->id}", [
            'titre' => 'Hacking',
            'description' => 'x',
            'adresse' => 'x',
            'ville' => 'x',
            'prix_par_nuit' => 1,
            'nombre_de_chambres' => 1,
            'amenities' => $amenities->pluck('id')->toArray(),
        ])->assertForbidden();
    }

    #[Test]
    public function invalid_amenity_id_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($owner)
            ->from('/annonces/create')
            ->post('/annonces', [
                'titre' => 'Test',
                'description' => 'Test',
                'adresse' => 'Test',
                'ville' => 'Test',
                'prix_par_nuit' => 100,
                'nombre_de_chambres' => 1,
                'amenities' => [99999],
            ]);

        $response->assertRedirect('/annonces/create');
        $response->assertSessionHasErrors('amenities.0');
    }
}
