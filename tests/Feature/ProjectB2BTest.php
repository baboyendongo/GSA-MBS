<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\projectB2B;
use Database\Seeders\ActivityStatesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectB2BTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ActivityStatesSeeder::class);
    }

    public function test_project_b2b_accepts_the_available_states(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['Achevée', 'En cours', 'Disponible'] as $state) {
            $this->post(route('projects-b2b.store'), [
                'date' => '2026-11-09',
                'project' => 'CRM B2B',
                'client' => 'Client test',
                'Action' => 'Action test',
                'responsable' => 'Responsable test',
                'etat' => $state,
                'delai' => '2026-11-15',
            ])->assertRedirect(route('projects-b2b.index'));
        }

        $this->assertDatabaseCount('project_b2_b_s', 3);
    }

    public function test_project_b2b_rejects_an_unknown_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('projects-b2b.index'))
            ->post(route('projects-b2b.store'), [
                'date' => '2026-11-09',
                'project' => 'CRM B2B',
                'client' => 'Client test',
                'Action' => 'Action test',
                'responsable' => 'Responsable test',
                'etat' => 'En attente',
                'delai' => '2026-11-15',
            ])
            ->assertRedirect(route('projects-b2b.index'))
            ->assertSessionHasErrors('etat');

        $this->assertDatabaseCount('project_b2_b_s', 0);
    }

    public function test_project_details_button_contains_values_for_the_popup(): void
    {
        $this->actingAs(User::factory()->create());

        projectB2B::create([
            'date' => '2026-11-09',
            'project' => 'CRM B2B',
            'client' => 'Client test',
            'Action' => 'Action test',
            'responsable' => 'B2B SI',
            'etat' => 'En cours',
            'delai' => '2026-11-15',
            'date_cloture' => '2026-11-09',
            'observation' => 'Retour attendu',
        ]);

        $this->get(route('projects-b2b.index'))
            ->assertOk()
            ->assertSee('data-project-detail', false)
            ->assertSee('data-project-deadline="15/11/2026"', false)
            ->assertSee('data-project-close-date="09/11/2026"', false)
            ->assertSee('data-project-observation="Retour attendu"', false)
            ->assertSee('id="project-detail-dialog"', false)
            ->assertSee('id="project-detail-close-date"', false);
    }
}