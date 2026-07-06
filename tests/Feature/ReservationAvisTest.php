<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\Avis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationAvisTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_make_a_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $this->actingAs($traveler);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => '2027-01-01',
            'end_date'   => '2027-01-05',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'annonce_id' => $annonce->id,
            'user_id'    => $traveler->id,
            'status'     => 'pending',
        ]);
    }

    #[Test]
    public function overlapping_dates_are_blocked()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler1 = User::factory()->create(['role' => 'voyageur']);
        $traveler2 = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        // First reservation
        Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler1->id,
            'start_date'  => '2027-02-01',
            'end_date'    => '2027-02-10',
            'total_price' => 500,
            'status'      => 'accepted',
        ]);

        // Second traveler tries same dates
        $this->actingAs($traveler2);
        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => '2027-02-05',
            'end_date'   => '2027-02-08',
        ]);

        $response->assertSessionHasErrors('dates');
    }

    #[Test]
    public function host_can_accept_a_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => '2027-03-01',
            'end_date'    => '2027-03-05',
            'total_price' => 400,
            'status'      => 'pending',
        ]);

        $this->actingAs($host);
        $this->patch("/reservations/{$reservation->id}/accept");

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'accepted',
        ]);
    }

    #[Test]
    public function host_can_refuse_a_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => '2027-04-01',
            'end_date'    => '2027-04-05',
            'total_price' => 400,
            'status'      => 'pending',
        ]);

        $this->actingAs($host);
        $this->patch("/reservations/{$reservation->id}/refuse");

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'refused',
        ]);
    }

    #[Test]
    public function traveler_can_cancel_a_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => '2027-05-01',
            'end_date'    => '2027-05-05',
            'total_price' => 400,
            'status'      => 'pending',
        ]);

        $this->actingAs($traveler);
        $this->patch("/reservations/{$reservation->id}/cancel");

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'cancelled',
        ]);
    }

    #[Test]
    public function user_can_leave_a_review_on_accepted_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => now()->subDays(10)->toDateString(),
            'end_date'    => now()->subDays(5)->toDateString(),
            'total_price' => 400,
            'status'      => 'accepted',
        ]);

        $this->actingAs($traveler);
        $response = $this->post("/reservations/{$reservation->id}/avis", [
            'rating'  => 5,
            'comment' => 'Séjour excellent !',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('avis', [
            'reservation_id' => $reservation->id,
            'user_id'        => $traveler->id,
            'rating'         => 5,
        ]);
    }

    #[Test]
    public function user_cannot_leave_a_review_on_pending_reservation()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => '2027-07-01',
            'end_date'    => '2027-07-05',
            'total_price' => 400,
            'status'      => 'pending',
        ]);

        $this->actingAs($traveler);
        $this->post("/reservations/{$reservation->id}/avis", [
            'rating'  => 4,
            'comment' => 'Bien.',
        ]);

        $this->assertDatabaseMissing('avis', [
            'reservation_id' => $reservation->id,
        ]);
    }
}