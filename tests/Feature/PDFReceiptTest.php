<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PDFReceiptTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function accepting_a_reservation_generates_a_receipt_file()
    {
        Storage::fake('local');

        $traveler = User::factory()->create(['role' => 'user']);
        $host = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 2500,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($host)->patch(route('reservations.accept', $reservation));

        $response->assertRedirect();
        Storage::disk('local')->assertExists("receipts/reservation-{$reservation->id}.pdf");
    }

    #[Test]
    public function traveler_can_download_receipt_for_accepted_reservation()
    {
        $traveler = User::factory()->create(['role' => 'user']);
        $host = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 2500,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($traveler)->get(route('reservations.receipt', $reservation));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function host_can_download_guest_receipt()
    {
        $traveler = User::factory()->create(['role' => 'user']);
        $host = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 2500,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($host)->get(route('reservations.receipt', $reservation));

        $response->assertOk();
    }

    #[Test]
    public function non_involved_user_cannot_download_receipt()
    {
        $traveler = User::factory()->create(['role' => 'user']);
        $host = User::factory()->create(['role' => 'user']);
        $stranger = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 2500,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($stranger)->get(route('reservations.receipt', $reservation));

        $response->assertForbidden();
    }

    #[Test]
    public function receipt_unavailable_for_pending_reservations()
    {
        $traveler = User::factory()->create(['role' => 'user']);
        $host = User::factory()->create(['role' => 'user']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'user_id' => $traveler->id,
            'annonce_id' => $annonce->id,
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'total_price' => 2500,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($traveler)->get(route('reservations.receipt', $reservation));

        $response->assertForbidden();
    }
}
