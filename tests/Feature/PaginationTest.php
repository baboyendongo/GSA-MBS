<?php

namespace Tests\Feature;

use App\Models\ActivityState;
use App\Models\instance as InstanceModel;
use App\Models\instanceLS;
use App\Models\projectB2B;
use App\Models\User;
use Database\Seeders\ActivityStatesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ActivityStatesSeeder::class);
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    public function test_tracking_lists_show_ten_records_and_keep_search_in_pagination(): void
    {
        for ($record = 1; $record <= 11; $record++) {
            instanceLS::create([
                'date' => '2026-11-09',
                'evenement' => 'LS-'.$record,
                'operation' => 'RSD',
                'phase' => 'Traitement',
            ]);
            InstanceModel::create([
                'date' => '2026-11-09',
                'instance' => 'Instance-'.$record,
                'client' => 'Client',
                'responsable' => 'MBS Tech',
                'etat' => 'En cours',
            ]);
            projectB2B::create([
                'date' => '2026-11-09',
                'project' => 'Project-'.$record,
                'client' => 'Client',
                'Action' => 'Action',
                'responsable' => 'B2B SI',
                'etat' => 'En cours',
                'delai' => '2026-11-15',
            ]);
        }

        $this->get(route('instancels.index'))
            ->assertOk()
            ->assertViewHas('instancels', fn ($records) => $records->count() === 10 && $records->total() === 11);
        $this->get(route('instancels.index', ['page' => 2]))
            ->assertViewHas('instancels', fn ($records) => $records->count() === 1 && $records->currentPage() === 2);

        $this->get(route('instances.index'))
            ->assertViewHas('instances', fn ($records) => $records->count() === 10 && $records->total() === 11);
        $this->get(route('instances.index', ['page' => 2]))
            ->assertViewHas('instances', fn ($records) => $records->count() === 1 && $records->currentPage() === 2);

        $this->get(route('projects-b2b.index'))
            ->assertViewHas('projects', fn ($records) => $records->count() === 10 && $records->total() === 11);
        $this->get(route('projects-b2b.index', ['page' => 2]))
            ->assertViewHas('projects', fn ($records) => $records->count() === 1 && $records->currentPage() === 2);

        $this->get(route('instancels.index', ['search' => 'LS-11', 'page' => 1]))
            ->assertViewHas('instancels', fn ($records) => $records->count() === 1 && $records->total() === 1);
    }

    public function test_admin_lists_show_ten_users_and_states_per_page(): void
    {
        User::factory(10)->create();
        ActivityState::create([
            'tracking_type' => 'instancels',
            'label' => 'Statut supplémentaire',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->get(route('admin.index'))
            ->assertOk()
            ->assertViewHas('users', fn ($users) => $users->count() === 10 && $users->total() === 11)
            ->assertViewHas('states', fn ($states) => $states->count() === 10 && $states->total() === 11);

        $this->get(route('admin.index', ['users_page' => 2, 'states_page' => 2]))
            ->assertViewHas('users', fn ($users) => $users->currentPage() === 2)
            ->assertViewHas('states', fn ($states) => $states->currentPage() === 2);
    }
}
