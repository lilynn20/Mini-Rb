<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Avis;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IssueFixesTest extends TestCase
{
    use RefreshDatabase;

    private function completedAcceptedReservation(User $traveler, User $host): Reservation
    {
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        return Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => '2020-06-01',
            'end_date'    => '2020-06-05',
            'total_price' => 400,
            'status'      => 'accepted',
        ]);
    }

    #[Test]
    public function a_second_review_on_the_same_reservation_is_blocked()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $reservation = $this->completedAcceptedReservation($traveler, $host);

        $this->actingAs($traveler);

        $this->post("/reservations/{$reservation->id}/avis", ['rating' => 5, 'comment' => 'Top !']);
        $this->post("/reservations/{$reservation->id}/avis", ['rating' => 1, 'comment' => 'Deuxième avis']);

        $this->assertEquals(1, Avis::where('reservation_id', $reservation->id)->count());
    }

    #[Test]
    public function a_review_is_blocked_before_the_stay_has_ended()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $annonce = Annonce::factory()->create(['user_id' => $host->id]);

        $reservation = Reservation::create([
            'annonce_id'  => $annonce->id,
            'user_id'     => $traveler->id,
            'start_date'  => now()->addDays(5)->toDateString(),
            'end_date'    => now()->addDays(10)->toDateString(),
            'total_price' => 400,
            'status'      => 'accepted',
        ]);

        $this->actingAs($traveler);
        $this->post("/reservations/{$reservation->id}/avis", ['rating' => 5, 'comment' => 'En avance']);

        $this->assertDatabaseMissing('avis', ['reservation_id' => $reservation->id]);
    }

    #[Test]
    public function an_error_flash_message_is_rendered_on_the_page()
    {
        $host = User::factory()->create(['role' => 'hote']);
        $traveler = User::factory()->create(['role' => 'voyageur']);
        $reservation = $this->completedAcceptedReservation($traveler, $host);
        Avis::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $traveler->id,
            'rating'         => 5,
            'comment'        => 'Premier avis',
        ]);

        $response = $this->actingAs($traveler)
            ->from('/')
            ->followingRedirects()
            ->post("/reservations/{$reservation->id}/avis", ['rating' => 3, 'comment' => 'Doublon']);

        $response->assertSee('Vous avez déjà laissé un avis pour cette réservation.');
    }

    #[Test]
    public function an_admin_cannot_delete_their_own_account()
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)->delete("/admin/users/{$admin1->id}");

        $this->assertDatabaseHas('users', ['id' => $admin1->id]);
    }

    #[Test]
    public function the_last_admin_cannot_be_deleted()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $other = User::factory()->create(['role' => 'admin']);
        $victimAdmin = $admin;

        $this->actingAs($other)->delete("/admin/users/{$victimAdmin->id}");
        $this->actingAs($other)->delete("/admin/users/{$other->id}");

        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    #[Test]
    public function a_non_admin_cannot_reach_the_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertRedirect('/');
    }

    #[Test]
    public function an_admin_can_reach_the_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->markEmailAsVerified();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }
}
