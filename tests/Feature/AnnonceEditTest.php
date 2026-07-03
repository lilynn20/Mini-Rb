<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnnonceEditTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_owner_can_edit_the_text_fields_of_an_annonce()
    {
        $owner = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id, 'titre' => 'Ancien titre']);

        $this->actingAs($owner)->put("/annonces/{$annonce->id}", [
            'titre'              => 'Nouveau titre',
            'description'        => 'Description mise à jour du logement.',
            'adresse'            => 'Rue de la Kasbah',
            'ville'              => 'Marrakech',
            'prix_par_nuit'      => 750,
            'nombre_de_chambres' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('annonces', [
            'id'    => $annonce->id,
            'titre' => 'Nouveau titre',
            'ville' => 'Marrakech',
        ]);
    }

    #[Test]
    public function the_owner_can_replace_the_image()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $file = UploadedFile::fake()->image('maison.jpg')->size(1200); // 1.2 MB, well under limit

        $this->actingAs($owner)->put("/annonces/{$annonce->id}", [
            'titre'              => $annonce->titre,
            'description'        => $annonce->description,
            'adresse'            => $annonce->adresse,
            'ville'              => $annonce->ville,
            'prix_par_nuit'      => $annonce->prix_par_nuit,
            'nombre_de_chambres' => $annonce->nombre_de_chambres,
            'images'             => [$file],
        ])->assertRedirect();

        $newImage = $annonce->fresh()->images->last();
        $this->assertNotNull($newImage, 'image should be created');
        Storage::disk('s3')->assertExists($newImage->image_path);
    }

    #[Test]
    public function an_oversized_image_is_rejected_with_a_visible_error_and_nothing_changes()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id, 'titre' => 'Titre original']);

        $tooBig = UploadedFile::fake()->image('enorme.jpg')->size(6000); // ~5.9 MB > 5 MB limit

        $response = $this->actingAs($owner)
            ->from("/annonces/{$annonce->id}/edit")
            ->put("/annonces/{$annonce->id}", [
                'titre'              => 'Tentative de modification',
                'description'        => $annonce->description,
                'adresse'            => $annonce->adresse,
                'ville'              => $annonce->ville,
                'prix_par_nuit'      => $annonce->prix_par_nuit,
                'nombre_de_chambres' => $annonce->nombre_de_chambres,
                'images'             => [$tooBig],
            ]);

        $response->assertRedirect("/annonces/{$annonce->id}/edit");
        $response->assertSessionHasErrors('images.0'); // error is flashed and now displayed in the form

        // The edit was fully rejected — title unchanged.
        $this->assertDatabaseHas('annonces', ['id' => $annonce->id, 'titre' => 'Titre original']);
    }

    #[Test]
    public function a_non_owner_cannot_edit_someone_elses_annonce()
    {
        $owner = User::factory()->create(['role' => 'user']);
        $stranger = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)->put("/annonces/{$annonce->id}", [
            'titre'              => 'Piratage',
            'description'        => 'x',
            'adresse'            => 'x',
            'ville'              => 'x',
            'prix_par_nuit'      => 1,
            'nombre_de_chambres' => 1,
        ])->assertForbidden();
    }
}