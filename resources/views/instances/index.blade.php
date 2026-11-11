@extends('layouts.app')

@section('title', 'Instance | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Instance')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Suivi opérationnel</p>
            <h1>Instance</h1>
            <p>Suivez les interventions, responsables et échéances.</p>
        </div>
        <button class="primary-button" id="open-instance-dialog" type="button"><span aria-hidden="true">+</span> Ajouter une instance</button>
    </div>

    <section class="table-panel" aria-labelledby="instances-title">
        <div class="table-toolbar">
            <div>
                <h2 class="table-title" id="instances-title">Toutes les instances</h2>
                <p class="table-count"><span id="instance-visible-count">{{ $instances->total() }}</span> enregistrement(s)</p>
            </div>
            <form class="search-wrap" method="get" action="{{ route('instances.index') }}">
                <button class="search-icon search-submit" type="submit" aria-label="Rechercher"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.8" cy="8.8" r="5.8" stroke="currentColor" stroke-width="1.7"/><path d="m13.2 13.2 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button>
                <input class="search-input" id="instance-search" name="search" type="search" value="{{ request('search') }}" placeholder="Rechercher une instance, un client..." autocomplete="off" aria-label="Rechercher dans les instances">
            </form>
        </div>
        <div class="table-scroll">
            <table class="instances-table">
                <thead><tr><th>Date</th><th>Instance</th><th>Client</th><th>Ligne</th><th>Responsable</th><th>État</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($instances as $instance)
                        <tr data-instance-row data-search="{{ mb_strtolower(implode(' ', array_filter([$instance->date, $instance->instance, $instance->client, $instance->ligne, $instance->responsable, $instance->etat, $instance->delai, $instance->date_cloture, $instance->observation])), 'UTF-8') }}">
                            <td class="date-cell">{{ $instance->date ? \Illuminate\Support\Carbon::parse($instance->date)->format('d/m/Y') : '—' }}</td>
                            <td class="event-number">{{ $instance->instance }}</td>
                            <td class="client-cell">{{ $instance->client }}</td>
                            <td>{{ $instance->ligne ?: '—' }}</td>
                            <td>{{ $instance->responsable }}</td>
                            <td>@if ($instance->etat)<span class="phase-badge">{{ $instance->etat }}</span>@else<span class="muted-cell">—</span>@endif</td>
                            <td><div class="row-actions"><button class="detail-button" type="button" data-instance-detail data-instance-title="{{ $instance->instance }}" data-instance-deadline="{{ $instance->delai }}" data-instance-close-date="{{ $instance->date_cloture ? \Illuminate\Support\Carbon::parse($instance->date_cloture)->format('d/m/Y') : '' }}" data-instance-observation="{{ $instance->observation }}" title="Afficher les détails" aria-haspopup="dialog" aria-label="Voir les détails de {{ $instance->instance }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg></button><button class="table-action-link" type="button" data-instance-edit data-record-id="{{ $instance->id }}" data-update-url="{{ route('instances.update', $instance->id) }}" data-date="{{ $instance->date }}" data-instance="{{ $instance->instance }}" data-client="{{ $instance->client }}" data-ligne="{{ $instance->ligne }}" data-responsable="{{ $instance->responsable }}" data-etat="{{ $instance->etat }}" data-delai="{{ $instance->delai }}" data-date-cloture="{{ $instance->date_cloture }}" data-observation="{{ $instance->observation }}" title="Modifier" aria-label="Modifier cette instance"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>@if (auth()->user()->is_admin)<button class="danger-button" type="button" data-instance-delete data-record-name="{{ $instance->instance }}" data-delete-url="{{ route('instances.destroy', $instance->id) }}" title="Supprimer" aria-label="Supprimer cette instance"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="7">Aucune instance enregistrée.</td></tr>
                    @endforelse
                    <tr id="instance-empty-search" hidden><td class="empty-state" colspan="7">Aucun résultat ne correspond à cette recherche.</td></tr>
                </tbody>
            </table>
        </div>
        @if ($instances->hasPages())
            <div class="pagination-wrap">{{ $instances->links() }}</div>
        @endif
    </section>

    <dialog class="instance-dialog" id="instance-dialog" @if ($errors->getBag('default')->any()) data-open="true" @endif>
        <div class="dialog-head"><div><h2>Ajouter une instance</h2><p>Renseignez les informations de suivi.</p></div><button class="close-button" id="close-instance-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('instances.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-field"><label for="instance-date">Date *</label><input id="instance-date" name="date" type="date" value="{{ old('date') }}" required autofocus>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="instance-name">Instance *</label><input id="instance-name" name="instance" value="{{ old('instance') }}" required>@error('instance')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="instance-client">Client *</label><input id="instance-client" name="client" value="{{ old('client') }}" required>@error('client')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="instance-line">Ligne</label><input id="instance-line" name="ligne" value="{{ old('ligne') }}"></div>
                <div class="form-field"><label for="instance-owner">Responsable *</label><input id="instance-owner" name="responsable" value="{{ old('responsable') }}" required>@error('responsable')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="instance-state">État</label><select id="instance-state" name="etat"><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('etat') === $state)>{{ $state }}</option>@endforeach</select></div>
                <div class="form-field"><label for="instance-deadline">Délais</label><input id="instance-deadline" name="delai" value="{{ old('delai') }}"></div>
                <div class="form-field"><label for="instance-close-date">Date de clôture</label><input id="instance-close-date" name="date_cloture" type="date" value="{{ old('date_cloture') }}">@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="instance-observation">Observations</label><textarea id="instance-observation" name="observation">{{ old('observation') }}</textarea>@error('observation')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instance-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Ajouter l’instance</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="instance-edit-dialog" @if (session()->has('edit_record_id')) data-open="true" data-record-id="{{ session('edit_record_id') }}" data-old-values="{{ json_encode(session()->getOldInput(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" @endif>
        <div class="dialog-head"><div><h2>Modifier une instance</h2><p>Modifiez les informations de suivi.</p></div><button class="close-button" id="close-instance-edit-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="instance-edit-form" method="post" action="">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-instance-date">Date *</label><input id="edit-instance-date" name="date" type="date" required>@error('date', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-name">Instance *</label><input id="edit-instance-name" name="instance" required>@error('instance', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-client">Client *</label><input id="edit-instance-client" name="client" required>@error('client', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-line">Ligne</label><input id="edit-instance-line" name="ligne"></div>
                <div class="form-field"><label for="edit-instance-owner">Responsable *</label><input id="edit-instance-owner" name="responsable" required>@error('responsable', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instance-state">État</label><select id="edit-instance-state" name="etat"><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select></div>
                <div class="form-field"><label for="edit-instance-deadline">Délais</label><input id="edit-instance-deadline" name="delai"></div>
                <div class="form-field"><label for="edit-instance-close-date">Date de clôture</label><input id="edit-instance-close-date" name="date_cloture" type="date">@error('date_cloture', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-instance-observation">Observations</label><textarea id="edit-instance-observation" name="observation"></textarea>@error('observation', 'instanceEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instance-edit-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog confirm-dialog" id="instance-delete-dialog">
        <div class="dialog-head"><div><h2>Supprimer cette instance ?</h2><p>Cette action est définitive.</p></div><button class="close-button" id="close-instance-delete-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="instance-delete-form" method="post" action="">
            @csrf
            @method('DELETE')
            <p>Confirmez la suppression de <strong id="instance-delete-name"></strong>.</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instance-delete-dialog" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Supprimer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog detail-dialog" id="instance-detail-dialog" aria-labelledby="instance-detail-title">
        <div class="dialog-head"><div><h2 id="instance-detail-title">Détails de l’instance</h2><p id="instance-detail-name"></p></div><button class="close-button" id="close-instance-detail-dialog" type="button" aria-label="Fermer">×</button></div>
        <div class="detail-content">
            <dl class="detail-list">
                <div><dt>Délais</dt><dd id="instance-detail-deadline">—</dd></div>
                <div><dt>Date clôture</dt><dd id="instance-detail-close-date">—</dd></div>
                <div><dt>Observations</dt><dd id="instance-detail-observation">—</dd></div>
            </dl>
        </div>
    </dialog>
@endsection
