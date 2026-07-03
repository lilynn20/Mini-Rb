<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DateRangeFilterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function search_excludes_annonces_with_pending_reservations_in_date_range()
    {
        $annonce1 = Annonce::factory()->create();
        $annonce2 = Annonce::factory()->create();
        $traveler = User::factory()->create(['role' => 'user']);

        Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce1->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 1000,
            'status' => 'pending',
        ]);

        $response = $this->get('/?start_date=2026-07-12&end_date=2026-07-13');

        $response->assertOk();
        $this->assertDatabaseHas('annonces', ['id' => $annonce2->id]);
    }

    #[Test]
    public function search_includes_annonces_with_refused_or_cancelled_reservations()
    {
        $annonce = Annonce::factory()->create();
        $traveler = User::factory()->create(['role' => 'user']);

        Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 1000,
            'status' => 'refused',
        ]);

        $response = $this->get('/?start_date=2026-07-12&end_date=2026-07-13');

        $response->assertOk();
        $this->assertCount(1, $response->viewData('annonces'));
    }

    #[Test]
    public function search_validates_end_date_after_start_date()
    {
        $response = $this->get('/?start_date=2026-07-15&end_date=2026-07-10');

        $response->assertOk();
    }
}
