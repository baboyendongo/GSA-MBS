@extends('layouts.app')

@section('title', 'Dashboard | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Vue d’ensemble</p><h1>Dashboard</h1><p>Un aperçu de vos suivis et projets en cours.</p></div>
        {{-- <a class="primary-button" href="{{ route('instancels.index') }}"><span aria-hidden="true">+</span> Nouvel Instancel</a> --}}
    </div>

    <section class="stats dashboard-stats" aria-label="Indicateurs clés">
        <div class="stat"><span class="stat-label">Instancels au total</span><strong class="stat-value">{{ $totalInstancels }}</strong><span class="stat-note">Tous les enregistrements</span></div>
        <div class="stat"><span class="stat-label">Instances</span><strong class="stat-value">{{ $totalInstances }}</strong><span class="stat-note">Suivis enregistrés</span></div>
        <div class="stat"><span class="stat-label">Instancels en cours</span><strong class="stat-value">{{ $activeInstancels }}</strong><span class="stat-note">Sans date de clôture</span></div>
        <div class="stat"><span class="stat-label">Clôturés</span><strong class="stat-value">{{ $closedInstancels }}</strong><span class="stat-note">Suivis terminés</span></div>
        <div class="stat"><span class="stat-label">Projets B2B</span><strong class="stat-value">{{ $totalProjectsB2B }}</strong><span class="stat-note">Projets enregistrés</span></div>
    </section>

    <div class="dashboard-grid">
        <section class="table-panel" aria-labelledby="recent-instancels-title">
            <div class="table-toolbar"><div><h2 class="table-title" id="recent-instancels-title">Instancels récents</h2><p class="table-count">Derniers événements enregistrés</p></div><a class="text-link" href="{{ route('instancels.index') }}">Tout voir <span aria-hidden="true">→</span></a></div>
            <div class="table-scroll"><table class="instances-table compact-table"><thead><tr><th>Date</th><th>Instancel</th><th>Client</th><th>État</th></tr></thead><tbody>
                @forelse ($recentInstancels as $instancel)
                    <tr><td class="date-cell">{{ $instancel->date ? \Illuminate\Support\Carbon::parse($instancel->date)->format('d/m/Y') : '—' }}</td><td class="event-number">{{ $instancel->evenement }}</td><td class="client-cell">{{ $instancel->clients ?: '—' }}</td><td>{{ $instancel->phase ?: '—' }}</td></tr>
                @empty
                    <tr><td class="empty-state" colspan="4">Aucun Instancel enregistré.</td></tr>
                @endforelse
            </tbody></table></div>
        </section>

        <section class="table-panel" aria-labelledby="recent-projects-title">
            <div class="table-toolbar"><div><h2 class="table-title" id="recent-projects-title">Project B2B</h2><p class="table-count">Projets suivis récemment</p></div><a class="text-link" href="{{ route('projects-b2b.index') }}">Tout voir <span aria-hidden="true">→</span></a></div>
            <div class="table-scroll"><table class="instances-table compact-table"><thead><tr><th>Projet</th><th>Client</th><th>État</th></tr></thead><tbody>
                @forelse ($recentProjectsB2B as $project)
                    <tr><td class="event-number">{{ $project->project }}</td><td class="client-cell">{{ $project->client }}</td><td><span class="phase-badge">{{ $project->etat }}</span></td></tr>
                @empty
                    <tr><td class="empty-state" colspan="3">Aucun projet B2B enregistré.</td></tr>
                @endforelse
            </tbody></table></div>
        </section>

        <section class="table-panel dashboard-instance-panel" aria-labelledby="recent-instances-title">
            <div class="table-toolbar"><div><h2 class="table-title" id="recent-instances-title">Instances récentes</h2><p class="table-count">Dernières instances enregistrées</p></div><a class="text-link" href="{{ route('instances.index') }}">Tout voir <span aria-hidden="true">→</span></a></div>
            <div class="table-scroll"><table class="instances-table compact-table"><thead><tr><th>Date</th><th>Instance</th><th>Client</th><th>Responsable</th><th>État</th></tr></thead><tbody>
                @forelse ($recentInstances as $instance)
                    <tr><td class="date-cell">{{ $instance->date ? \Illuminate\Support\Carbon::parse($instance->date)->format('d/m/Y') : '—' }}</td><td class="event-number">{{ $instance->instance }}</td><td class="client-cell">{{ $instance->client }}</td><td>{{ $instance->responsable }}</td><td>{{ $instance->etat ?: '—' }}</td></tr>
                @empty
                    <tr><td class="empty-state" colspan="5">Aucune instance enregistrée.</td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    </div>
@endsection
