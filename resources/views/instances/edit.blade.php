@extends('layouts.app')

@section('title', 'Modifier Instance | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Modifier Instance')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Instance</p><h1>Modifier une instance</h1></div>
        <a class="text-link" href="{{ route('instances.index') }}">Retour à la liste</a>
    </div>

    <section class="table-panel edit-panel">
        <form class="dialog-form" method="post" action="{{ route('instances.update', $instance->id) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-instance-date">Date *</label><input id="edit-instance-date" name="date" type="date" value="{{ old('date', $instance->date) }}" required>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-name">Instance *</label><input id="edit-instance-name" name="instance" value="{{ old('instance', $instance->instance) }}" required>@error('instance')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-client">Client *</label><input id="edit-instance-client" name="client" value="{{ old('client', $instance->client) }}" required>@error('client')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-line">Ligne</label><input id="edit-instance-line" name="ligne" value="{{ old('ligne', $instance->ligne) }}"></div>
                <div class="form-field"><label for="edit-instance-owner">Responsable *</label><input id="edit-instance-owner" name="responsable" value="{{ old('responsable', $instance->responsable) }}" required>@error('responsable')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-state">État</label><select id="edit-instance-state" name="etat"><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('etat', $instance->etat) === $state)>{{ $state }}</option>@endforeach</select></div>
                <div class="form-field"><label for="edit-instance-deadline">Délais</label><input id="edit-instance-deadline" name="delai" value="{{ old('delai', $instance->delai) }}"></div>
                <div class="form-field"><label for="edit-instance-close-date">Date de clôture</label><input id="edit-instance-close-date" name="date_cloture" type="date" value="{{ old('date_cloture', $instance->date_cloture) }}">@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-instance-observation">Observations</label><textarea id="edit-instance-observation" name="observation">{{ old('observation', $instance->observation) }}</textarea>@error('observation')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><a class="secondary-button" href="{{ route('instances.index') }}">Annuler</a><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </section>
@endsection
