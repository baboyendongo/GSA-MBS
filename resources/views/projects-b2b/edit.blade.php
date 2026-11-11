@extends('layouts.app')

@section('title', 'Modifier Project B2B | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Modifier Project B2B')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Project B2B</p><h1>Modifier un projet</h1></div>
        <a class="text-link" href="{{ route('projects-b2b.index') }}">Retour à la liste</a>
    </div>

    <section class="table-panel edit-panel">
        <form class="dialog-form" method="post" action="{{ route('projects-b2b.update', $project->id) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-project-date">Date *</label><input id="edit-project-date" name="date" type="date" value="{{ old('date', $project->date) }}" required>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-name">Projet *</label><input id="edit-project-name" name="project" value="{{ old('project', $project->project) }}" required>@error('project')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-client">Client *</label><input id="edit-project-client" name="client" value="{{ old('client', $project->client) }}" required>@error('client')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-action">Action *</label><input id="edit-project-action" name="Action" value="{{ old('Action', $project->Action) }}" required>@error('Action')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-owner">Responsable *</label><input id="edit-project-owner" name="responsable" value="{{ old('responsable', $project->responsable) }}" required>@error('responsable')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-state">État *</label><select id="edit-project-state" name="etat" required><option value="" disabled>Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('etat', $project->etat) === $state)>{{ $state }}</option>@endforeach</select>@error('etat')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-deadline">Délai *</label><input id="edit-project-deadline" name="delai" value="{{ old('delai', $project->delai) }}" required>@error('delai')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-project-close-date">Date de clôture</label><input id="edit-project-close-date" name="date_cloture" type="date" value="{{ old('date_cloture', $project->date_cloture) }}">@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-project-observation">Observation</label><textarea id="edit-project-observation" name="observation">{{ old('observation', $project->observation) }}</textarea>@error('observation')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><a class="secondary-button" href="{{ route('projects-b2b.index') }}">Annuler</a><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </section>
@endsection
