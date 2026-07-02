<?php

namespace Tests\Feature;

use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_verification_email_actually_contains_a_clickable_verification_link()
    {
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id'   => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $rendered = (new VerifyEmailMail($url, $user->name))->render();

        // The whole point of the bug: the signed verify URL must be present in the email body.
        // Blade escapes '&' to '&amp;' inside the href, which is valid HTML, so compare the escaped form.
        $this->assertStringContainsString(e($url), $rendered);
        $this->assertStringContainsString('/email/verify/', $rendered);
        $this->assertStringContainsString('signature=', $rendered);
        $this->assertStringContainsString('Vérifier mon email', $rendered);
    }

    #[Test]
    public function registering_sends_the_verification_mail_and_redirects_to_the_notice()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        Mail::assertSent(VerifyEmailMail::class, fn ($mail) => $mail->hasTo('jane@example.com'));
    }

    #[Test]
    public function clicking_the_link_verifies_the_account_and_shows_the_green_banner_on_home()
    {
        Event::fake([Verified::class]);

        $user = User::create([
            'name'     => 'Click Me',
            'email'    => 'click@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $this->assertNull($user->email_verified_at);

        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id'   => $user->getKey(),
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success'); // green banner is rendered from session('success')
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    #[Test]
    public function an_already_verified_user_visiting_the_notice_is_bounced_home()
    {
        $user = User::create([
            'name'     => 'Already Verified',
            'email'    => 'done@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);
        $user->markEmailAsVerified(); // email_verified_at is guarded (not in $fillable)

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
    }
}