@extends('layouts.app')

@section('title', 'Modifier Instancel | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Modifier Instancel')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Instancels</p><h1>Modifier un Instancel</h1></div>
        <a class="text-link" href="{{ route('instancels.index') }}">Retour à la liste</a>
    </div>

    <section class="table-panel edit-panel">
        <form class="dialog-form" method="post" action="{{ route('instancels.update', $instancel->id) }}" data-archive-state-field="phase" data-close-date-field-name="date_cloture">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-instancel-date">Date *</label><input id="edit-instancel-date" name="date" type="date" value="{{ old('date', $instancel->date) }}" required>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instancel-name">Instancel *</label><input id="edit-instancel-name" name="evenement" value="{{ old('evenement', $instancel->evenement) }}" required>@error('evenement')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instancel-client">Client</label><input id="edit-instancel-client" name="clients" value="{{ old('clients', $instancel->clients) }}"></div>
                <div class="form-field"><label for="edit-instancel-line">Ligne</label><input id="edit-instancel-line" name="ligne" value="{{ old('ligne', $instancel->ligne) }}"></div>
                <div class="form-field"><label for="edit-instancel-state">État</label><select id="edit-instancel-state" name="phase" data-archive-state><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('phase', $instancel->phase) === $state)>{{ $state }}</option>@endforeach</select></div>
                <div class="form-field"><label for="edit-instancel-owner">Responsable</label><input id="edit-instancel-owner" name="responsable" value="{{ old('responsable', $instancel->responsable) }}"></div>
                <div class="form-field"><label for="edit-instancel-deadline">Délais</label><input id="edit-instancel-deadline" name="delai" type="date" value="{{ old('delai', $instancel->delai) }}"></div>
                <div class="form-field" data-close-date-field @if (old('phase', $instancel->phase) !== 'Archivé') hidden @endif><label for="edit-instancel-close-date">Date de clôture</label><input id="edit-instancel-close-date" name="date_cloture" type="date" value="{{ old('date_cloture', $instancel->date_cloture) }}" @disabled(old('phase', $instancel->phase) !== 'Archivé')>@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-instancel-observation">Observations</label><textarea id="edit-instancel-observation" name="observation">{{ old('observation', $instancel->observation) }}</textarea>@error('observation')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><a class="secondary-button" href="{{ route('instancels.index') }}">Annuler</a><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </section>
@endsection
