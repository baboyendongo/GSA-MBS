<?php

namespace Tests\Feature;

use App\Models\instance as InstanceModel;
use App\Models\instanceLS;
use App\Models\projectB2B;
use App\Models\User;
use Database\Seeders\ActivityStatesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ActivityStatesSeeder::class);
    }

    public function test_new_instancel_accepts_only_the_listed_states(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        foreach (['Intégration', 'Traitement', 'en cours'] as $state) {
            $this->post(route('instancels.store'), [
                'date' => '2026-11-09',
                'evenement' => 'Événement '.$state,
                'phase' => $state,
            ])->assertRedirect(route('instancels.index'));
        }

        $this->assertDatabaseCount('instance_l_s', 3);

        $this->from(route('instancels.index'))
            ->post(route('instancels.store'), [
                'date' => '2026-11-09',
                'evenement' => 'Événement invalide',
                'phase' => 'Statut inconnu',
            ])
            ->assertRedirect(route('instancels.index'))
            ->assertSessionHasErrors('phase');

        $this->assertDatabaseCount('instance_l_s', 3);
    }

    public function test_instancel_can_be_edited_and_deleted_without_losing_hidden_fields(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $instancel = instanceLS::create([
            'date' => '2026-11-01',
            'evenement' => 'Ancien événement',
            'operation' => 'RSD',
            'codeclient' => '12345',
            'ville' => 'NKC',
        ]);

        $this->get(route('instancels.edit', $instancel->id))->assertOk();

        $this->put(route('instancels.update', $instancel->id), [
            'date' => '2026-11-02',
            'evenement' => 'Événement modifié',
            'clients' => 'Client test',
            'ligne' => '011791',
            'phase' => 'en cours',
            'responsable' => 'MBS Tech',
            'delai' => '2026-11-15',
            'date_cloture' => null,
            'observation' => 'Observation test',
        ])->assertRedirect(route('instancels.index'));

        $this->assertDatabaseHas('instance_l_s', [
            'id' => $instancel->id,
            'evenement' => 'Événement modifié',
            'operation' => 'RSD',
            'codeclient' => '12345',
            'ville' => 'NKC',
        ]);

        $this->delete(route('instancels.destroy', $instancel->id))
            ->assertRedirect(route('instancels.index'));

        $this->assertDatabaseMissing('instance_l_s', ['id' => $instancel->id]);
    }

    public function test_invalid_instancel_edit_reopens_its_edit_dialog(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $instancel = instanceLS::create([
            'date' => '2026-11-01',
            'evenement' => 'Événement test',
            'operation' => 'RSD',
        ]);

        $this->from(route('instancels.index'))
            ->put(route('instancels.update', $instancel->id), [
                'date' => 'date invalide',
                'evenement' => '',
            ])
            ->assertRedirect(route('instancels.index'))
            ->assertSessionHasErrorsIn('instancelEdit', ['date', 'evenement']);

        $this->get(route('instancels.index'))
            ->assertOk()
            ->assertSee('id="instancel-edit-dialog"', false)
            ->assertSee('data-open="true"', false)
            ->assertSee('data-record-id="'.$instancel->id.'"', false)
            ->assertDontSee('id="instancel-dialog" data-open="true"', false);
    }

    public function test_instance_can_be_edited_and_deleted(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $instance = InstanceModel::create([
            'date' => '2026-11-01',
            'instance' => 'DRG FTTH',
            'client' => 'SNDE',
            'responsable' => 'MBS Tech',
        ]);

        $this->get(route('instances.edit', $instance->id))->assertOk();

        $this->put(route('instances.update', $instance->id), [
            'date' => '2026-11-02',
            'instance' => 'DRG FTTH modifiée',
            'client' => 'SNDE',
            'ligne' => '011791',
            'responsable' => 'MBS Tech',
            'etat' => 'En cours',
            'delai' => null,
            'date_cloture' => null,
            'observation' => null,
        ])->assertRedirect(route('instances.index'));

        $this->assertDatabaseHas('instances', [
            'id' => $instance->id,
            'instance' => 'DRG FTTH modifiée',
            'ligne' => '011791',
            'etat' => 'En cours',
        ]);

        $this->delete(route('instances.destroy', $instance->id))
            ->assertRedirect(route('instances.index'));

        $this->assertDatabaseMissing('instances', ['id' => $instance->id]);
    }

    public function test_project_b2b_can_be_edited_and_deleted(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $project = projectB2B::create([
            'date' => '2026-11-01',
            'project' => 'CRM B2B',
            'client' => 'Client test',
            'Action' => 'Analyse du besoin',
            'responsable' => 'B2B SI',
            'etat' => 'En cours',
            'delai' => '2026-11-15',
        ]);

        $this->get(route('projects-b2b.edit', $project->id))->assertOk();

        $this->put(route('projects-b2b.update', $project->id), [
            'date' => '2026-11-02',
            'project' => 'CRM B2B',
            'client' => 'Client test',
            'Action' => 'Analyse terminée',
            'responsable' => 'B2B SI',
            'etat' => 'Archivé',
            'delai' => '2026-11-15',
            'date_cloture' => '2026-11-02',
            'observation' => 'Validation reçue',
        ])->assertRedirect(route('projects-b2b.index'));

        $this->assertDatabaseHas('project_b2_b_s', [
            'id' => $project->id,
            'Action' => 'Analyse terminée',
            'etat' => 'Archivé',
            'observation' => 'Validation reçue',
        ]);

        $this->delete(route('projects-b2b.destroy', $project->id))
            ->assertRedirect(route('projects-b2b.index'));

        $this->assertDatabaseMissing('project_b2_b_s', ['id' => $project->id]);
    }
}
