@extends('layouts.app')

@section('title', 'Project B2B | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Project B2B')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Portefeuille clients</p><h1>Project B2B</h1><p>Suivez les actions, responsables et échéances de vos projets.</p></div>
        <button class="primary-button" id="open-project-dialog" type="button"><span aria-hidden="true">+</span> Ajouter un projet</button>
    </div>

    <section class="table-panel" aria-labelledby="projects-title">
        <div class="table-toolbar">
            <div><h2 class="table-title" id="projects-title">Tous les projets B2B</h2><p class="table-count"><span id="project-visible-count">{{ $projects->total() }}</span> projet(s)</p></div>
            <form class="search-wrap" method="get" action="{{ route('projects-b2b.index') }}"><button class="search-icon search-submit" type="submit" aria-label="Rechercher"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.8" cy="8.8" r="5.8" stroke="currentColor" stroke-width="1.7"/><path d="m13.2 13.2 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button><input class="search-input" id="project-search" name="search" type="search" value="{{ request('search') }}" placeholder="Rechercher un projet, un client..." autocomplete="off" aria-label="Rechercher dans les projets B2B"></form>
        </div>
        <div class="table-scroll"><table class="instances-table"><thead><tr><th>Date</th><th>Projet</th><th>Client</th><th>Action</th><th>Responsable</th><th>État</th><th>Actions</th></tr></thead><tbody>
            @forelse ($projects as $project)
                <tr data-project-row data-search="{{ mb_strtolower(implode(' ', array_filter([$project->date, $project->project, $project->client, $project->Action, $project->responsable, $project->etat, $project->delai, $project->date_cloture, $project->observation])), 'UTF-8') }}">
                    <td class="date-cell">{{ $project->date ? \Illuminate\Support\Carbon::parse($project->date)->format('d/m/Y') : '—' }}</td><td class="event-number">{{ $project->project }}</td><td class="client-cell">{{ $project->client }}</td><td>{{ $project->Action }}</td><td>{{ $project->responsable }}</td><td><span class="phase-badge">{{ $project->etat }}</span></td>
                    <td><div class="row-actions"><button class="detail-button" type="button" data-project-detail data-project-title="{{ $project->project }}" data-project-deadline="{{ $project->delai }}" data-project-close-date="{{ $project->date_cloture ? \Illuminate\Support\Carbon::parse($project->date_cloture)->format('d/m/Y') : '' }}" data-project-observation="{{ $project->observation }}" title="Afficher les détails" aria-haspopup="dialog" aria-label="Voir les détails de {{ $project->project }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg></button><button class="table-action-link" type="button" data-project-edit data-record-id="{{ $project->id }}" data-update-url="{{ route('projects-b2b.update', $project->id) }}" data-date="{{ $project->date }}" data-project="{{ $project->project }}" data-client="{{ $project->client }}" data-action="{{ $project->Action }}" data-responsable="{{ $project->responsable }}" data-etat="{{ $project->etat }}" data-delai="{{ $project->delai }}" data-date-cloture="{{ $project->date_cloture }}" data-observation="{{ $project->observation }}" title="Modifier" aria-label="Modifier ce projet B2B"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>@if (auth()->user()->is_admin)<button class="danger-button" type="button" data-project-delete data-record-name="{{ $project->project }}" data-delete-url="{{ route('projects-b2b.destroy', $project->id) }}" title="Supprimer" aria-label="Supprimer ce projet B2B"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button>@endif</div></td>
                </tr>
            @empty
                <tr><td class="empty-state" colspan="7">Aucun projet B2B. Ajoutez votre premier projet pour commencer.</td></tr>
            @endforelse
            <tr id="project-empty-search" hidden><td class="empty-state" colspan="7">Aucun résultat ne correspond à cette recherche.</td></tr>
        </tbody></table></div>
        @if ($projects->hasPages())
            <div class="pagination-wrap">{{ $projects->links() }}</div>
        @endif
    </section>

    <dialog class="instance-dialog" id="project-dialog" @if ($errors->getBag('default')->any()) data-open="true" @endif>
        <div class="dialog-head"><div><h2>Ajouter un projet B2B</h2><p>Renseignez les informations principales du projet.</p></div><button class="close-button" id="close-project-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('projects-b2b.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-field"><label for="project-date">Date *</label><input id="project-date" name="date" type="date" value="{{ old('date') }}" required>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-name">Projet *</label><input id="project-name" name="project" value="{{ old('project') }}" required>@error('project')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-client">Client *</label><input id="project-client" name="client" value="{{ old('client') }}" required>@error('client')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-action">Action *</label><input id="project-action" name="Action" value="{{ old('Action') }}" required>@error('Action')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-owner">Responsable *</label><input id="project-owner" name="responsable" value="{{ old('responsable') }}" required>@error('responsable')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-status">État *</label><select id="project-status" name="etat" required><option value="" disabled @selected(old('etat') === null)>Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('etat') === $state)>{{ $state }}</option>@endforeach</select>@error('etat')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-deadline">Délai *</label><input id="project-deadline" name="delai" value="{{ old('date') }}" required>@error('delai')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="project-close-date">Date de clôture</label><input id="project-close-date" name="date_cloture" type="date" value="{{ old('date_cloture') }}">@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="project-observation">Observation</label><textarea id="project-observation" name="observation">{{ old('observation') }}</textarea>@error('observation')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-project-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Ajouter le projet</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="project-edit-dialog" @if (session()->has('edit_record_id')) data-open="true" data-record-id="{{ session('edit_record_id') }}" data-old-values="{{ json_encode(session()->getOldInput(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" @endif>
        <div class="dialog-head"><div><h2>Modifier un projet B2B</h2><p>Modifiez les informations du projet.</p></div><button class="close-button" id="close-project-edit-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="project-edit-form" method="post" action="">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-project-date">Date *</label><input id="edit-project-date" name="date" type="date" required>@error('date', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-name">Projet *</label><input id="edit-project-name" name="project" required>@error('project', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-client">Client *</label><input id="edit-project-client" name="client" required>@error('client', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-action">Action *</label><input id="edit-project-action" name="Action" required>@error('Action', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-owner">Responsable *</label><input id="edit-project-owner" name="responsable" required>@error('responsable', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-state">État *</label><select id="edit-project-state" name="etat" required><option value="" disabled>Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select>@error('etat', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-deadline">Délai *</label><input id="edit-project-deadline" name="delai" required>@error('delai', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-close-date">Date de clôture</label><input id="edit-project-close-date" name="date_cloture" type="date">@error('date_cloture', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-project-observation">Observation</label><textarea id="edit-project-observation" name="observation"></textarea>@error('observation', 'projectEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-project-edit-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog confirm-dialog" id="project-delete-dialog">
        <div class="dialog-head"><div><h2>Supprimer ce projet ?</h2><p>Cette action est définitive.</p></div><button class="close-button" id="close-project-delete-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="project-delete-form" method="post" action="">
            @csrf
            @method('DELETE')
            <p>Confirmez la suppression de <strong id="project-delete-name"></strong>.</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-project-delete-dialog" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Supprimer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog detail-dialog" id="project-detail-dialog" aria-labelledby="project-detail-title">
        <div class="dialog-head"><div><h2 id="project-detail-title">Détails du projet</h2><p id="project-detail-name"></p></div><button class="close-button" id="close-project-detail-dialog" type="button" aria-label="Fermer">×</button></div>
        <div class="detail-content">
            <dl class="detail-list">
                <div><dt>Délai</dt><dd id="project-detail-deadline">—</dd></div>
                <div><dt>Date de clôture</dt><dd id="project-detail-close-date">—</dd></div>
                <div><dt>Observation</dt><dd id="project-detail-observation">—</dd></div>
            </dl>
        </div>
    </dialog>
@endsection
