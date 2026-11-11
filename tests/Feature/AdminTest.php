<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ActivityState;
use App\Models\instanceLS;
use Database\Seeders\ActivityStatesSeeder;
use App\Models\instance as InstanceModel;
use App\Models\projectB2B;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ActivityStatesSeeder::class);
    }

    public function test_guest_is_redirected_from_administration(): void
    {
        $this->get(route('admin.index'))->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_open_administration(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_regular_user_cannot_see_or_delete_tracking_records(): void
    {
        $instancel = instanceLS::create([
            'date' => '2026-11-01',
            'evenement' => 'Événement test',
            'operation' => 'RSD',
        ]);
        $instance = InstanceModel::create([
            'date' => '2026-11-01',
            'instance' => 'Instance test',
            'client' => 'Client test',
            'responsable' => 'MBS Tech',
        ]);
        $project = projectB2B::create([
            'date' => '2026-11-01',
            'project' => 'Project test',
            'client' => 'Client test',
            'Action' => 'Action test',
            'responsable' => 'B2B SI',
            'etat' => 'En cours',
            'delai' => '2026-11-15',
        ]);

        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get(route('instancels.index'))->assertOk()->assertDontSee('data-instancel-delete', false);
        $this->get(route('instances.index'))->assertOk()->assertDontSee('data-instance-delete', false);
        $this->get(route('projects-b2b.index'))->assertOk()->assertDontSee('data-project-delete', false);

        $this->delete(route('instancels.destroy', $instancel))->assertForbidden();
        $this->delete(route('instances.destroy', $instance))->assertForbidden();
        $this->delete(route('projects-b2b.destroy', $project))->assertForbidden();

        $this->assertDatabaseHas('instance_l_s', ['id' => $instancel->id]);
        $this->assertDatabaseHas('instances', ['id' => $instance->id]);
        $this->assertDatabaseHas('project_b2_b_s', ['id' => $project->id]);
    }

    public function test_admin_can_view_statistics_and_user_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Administration')
            ->assertSee('Comptes utilisateurs')
            ->assertSee($admin->email);
    }

    public function test_admin_can_create_edit_and_delete_a_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'Nouvel utilisateur',
            'email' => 'new.user@example.com',
            'password' => 'secure-passphrase-2026',
            'password_confirmation' => 'secure-passphrase-2026',
        ])->assertRedirect(route('admin.index'));

        $user = User::where('email', 'new.user@example.com')->firstOrFail();
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('secure-passphrase-2026', $user->password));

        $this->put(route('admin.users.update', $user), [
            'name' => 'Utilisateur modifié',
            'email' => $user->email,
            'is_admin' => '1',
        ])->assertRedirect(route('admin.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Utilisateur modifié',
            'is_admin' => true,
        ]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_last_admin_cannot_be_deleted_or_demoted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
        ])->assertRedirect(route('admin.index'))
            ->assertSessionHasErrorsIn('userEdit', 'is_admin');

        $this->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.index'))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_admin' => true]);
    }

    public function test_admin_create_command_promotes_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'ndongo@gmail.com', 'is_admin' => false]);

        $this->artisan('admin:create', ['email' => $user->email])
            ->expectsOutput('Le compte existant a été promu administrateur.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_admin' => true]);
    }

    public function test_users_seeder_creates_accounts_idempotently_and_promotes_admin(): void
    {
        $existingAdminAccount = User::factory()->create([
            'name' => 'Compte existant',
            'email' => 'ndongo@gmail.com',
            'is_admin' => false,
        ]);

        config()->set('seeded_accounts.accounts', [
            [
                'name' => 'Administrateur MBS',
                'email' => 'ndongo@gmail.com',
                'password' => 'admin-seed-password-2026',
                'is_admin' => true,
            ],
            [
                'name' => 'Utilisateur MBS',
                'email' => 'user.mbs@example.com',
                'password' => 'regular-seed-password-2026',
                'is_admin' => false,
            ],
        ]);

        $this->seed(UsersSeeder::class);
        $this->seed(UsersSeeder::class);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'id' => $existingAdminAccount->id,
            'email' => 'ndongo@gmail.com',
            'is_admin' => true,
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user.mbs@example.com',
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_add_rename_disable_and_delete_unused_states(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post(route('admin.states.store'), [
            'tracking_type' => 'instancels',
            'label' => 'À valider',
        ])->assertRedirect(route('admin.index'));

        $state = ActivityState::where('tracking_type', 'instancels')->where('label', 'À valider')->firstOrFail();

        $this->put(route('admin.states.update', $state), ['label' => 'À traiter'])
            ->assertRedirect(route('admin.index'));

        $state->refresh();
        $this->assertSame('À traiter', $state->label);

        $this->patch(route('admin.states.toggle', $state))
            ->assertRedirect(route('admin.index'));

        $this->assertFalse($state->fresh()->is_active);

        $this->delete(route('admin.states.destroy', $state))
            ->assertRedirect(route('admin.index'));

        $this->assertDatabaseMissing('activity_states', ['id' => $state->id]);
    }

    public function test_renaming_a_state_updates_existing_records_and_used_state_cannot_be_disabled(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $state = ActivityState::where('tracking_type', 'instancels')->where('label', 'Intégration')->firstOrFail();
        $instancel = instanceLS::create([
            'date' => '2026-11-09',
            'evenement' => 'Migration état',
            'operation' => 'RSD',
            'phase' => 'Intégration',
        ]);

        $this->put(route('admin.states.update', $state), ['label' => 'Intégration réseau'])
            ->assertRedirect(route('admin.index'));

        $this->assertDatabaseHas('instance_l_s', [
            'id' => $instancel->id,
            'phase' => 'Intégration réseau',
        ]);

        $this->patch(route('admin.states.toggle', $state->fresh()))
            ->assertRedirect(route('admin.index'))
            ->assertSessionHasErrors('state');

        $this->delete(route('admin.states.destroy', $state->fresh()))
            ->assertRedirect(route('admin.index'))
            ->assertSessionHasErrors('state');
    }
}
