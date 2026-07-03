<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiImageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function owner_can_upload_multiple_images_to_annonce()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $this->actingAs($owner);

        $files = [
            UploadedFile::fake()->image('img1.jpg')->size(1200),
            UploadedFile::fake()->image('img2.png')->size(1500),
        ];

        $response = $this->post('/annonces', [
            'titre' => 'Test Annonce',
            'description' => 'Description test',
            'adresse' => 'Rue Test',
            'ville' => 'Agadir',
            'prix_par_nuit' => 500,
            'nombre_de_chambres' => 2,
            'images' => $files,
        ]);

        $response->assertRedirect();
        $annonce = Annonce::latest()->first();
        $this->assertEquals(2, $annonce->images->count());
        $this->assertTrue($annonce->images->first()->is_primary);
        $this->assertFalse($annonce->images->last()->is_primary);
    }

    #[Test]
    public function owner_can_add_images_to_existing_annonce()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)->put("/annonces/{$annonce->id}", [
            'titre' => $annonce->titre,
            'description' => $annonce->description,
            'adresse' => $annonce->adresse,
            'ville' => $annonce->ville,
            'prix_par_nuit' => $annonce->prix_par_nuit,
            'nombre_de_chambres' => $annonce->nombre_de_chambres,
            'images' => [
                UploadedFile::fake()->image('new1.jpg')->size(1200),
                UploadedFile::fake()->image('new2.jpg')->size(1200),
            ],
        ])->assertRedirect();

        $annonce->refresh();
        $this->assertEquals(2, $annonce->images->count());
    }

    #[Test]
    public function oversized_image_is_rejected()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $this->actingAs($owner);

        $tooBig = UploadedFile::fake()->image('huge.jpg')->size(6000);

        $response = $this->from('/annonces/create')
            ->post('/annonces', [
                'titre' => 'Test',
                'description' => 'Test',
                'adresse' => 'Test',
                'ville' => 'Test',
                'prix_par_nuit' => 100,
                'nombre_de_chambres' => 1,
                'images' => [$tooBig],
            ]);

        $response->assertRedirect('/annonces/create');
        $response->assertSessionHasErrors('images.0');
        $this->assertEquals(0, Annonce::count());
    }

    #[Test]
    public function s3_image_is_deleted_when_annonce_deleted()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);

        $this->actingAs($owner)->post('/annonces', [
            'titre' => 'Test',
            'description' => 'Test',
            'adresse' => 'Test',
            'ville' => 'Test',
            'prix_par_nuit' => 100,
            'nombre_de_chambres' => 1,
            'images' => [UploadedFile::fake()->image('test.jpg')->size(1200)],
        ]);

        $annonce = Annonce::latest()->first()->load('images');
        $imagePath = $annonce->images->first()->image_path;

        Storage::disk('s3')->assertExists($imagePath);

        $this->delete("/annonces/{$annonce->id}");

        Storage::disk('s3')->assertMissing($imagePath);
    }

    #[Test]
    public function first_uploaded_image_is_marked_primary()
    {
        Storage::fake('s3');

        $owner = User::factory()->create(['role' => 'user']);
        $this->actingAs($owner);

        $this->post('/annonces', [
            'titre' => 'Test',
            'description' => 'Test',
            'adresse' => 'Test',
            'ville' => 'Test',
            'prix_par_nuit' => 100,
            'nombre_de_chambres' => 1,
            'images' => [
                UploadedFile::fake()->image('first.jpg')->size(1200),
                UploadedFile::fake()->image('second.jpg')->size(1200),
                UploadedFile::fake()->image('third.jpg')->size(1200),
            ],
        ]);

        $annonce = Annonce::latest()->first();
        $this->assertTrue($annonce->images->where('sort_order', 0)->first()->is_primary);
        $this->assertFalse($annonce->images->where('sort_order', 1)->first()->is_primary);
    }
}
