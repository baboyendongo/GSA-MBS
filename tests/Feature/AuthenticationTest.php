<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Connexion');
    }

    public function test_guest_is_redirected_to_login_from_private_pages(): void
    {
        foreach (['/dashboard', '/instancels', '/instances', '/project-b2b'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_user_can_log_in(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_can_open_profile_page(): void
    {
        $user = User::factory()->create(['name' => 'Utilisateur Profil']);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Utilisateur Profil')
            ->assertSee('Changer le mot de passe')
            ->assertSee('8 caractères minimum');
    }

    public function test_user_can_change_their_password(): void
    {
        $user = User::factory()->create([
            'password' => 'current-password-2026',
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('password.update'), [
                'current_password' => 'current-password-2026',
                'password' => 'new-secure-password-2026',
                'password_confirmation' => 'new-secure-password-2026',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-secure-password-2026', $user->fresh()->password));
    }

    public function test_password_must_have_at_least_eight_characters(): void
    {
        $user = User::factory()->create([
            'password' => 'current-password-2026',
        ]);

        $this->actingAs($user)
            ->from(route('profile'))
            ->put(route('password.update'), [
                'current_password' => 'current-password-2026',
                'password' => 'court',
                'password_confirmation' => 'court',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrorsIn('passwordChange', 'password');

        $this->assertTrue(Hash::check('current-password-2026', $user->fresh()->password));
    }

    public function test_user_cannot_change_password_with_invalid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'current-password-2026',
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->put(route('password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password-2026',
                'password_confirmation' => 'new-secure-password-2026',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrorsIn('passwordChange', 'current_password');

        $this->assertTrue(Hash::check('current-password-2026', $user->fresh()->password));
    }
}