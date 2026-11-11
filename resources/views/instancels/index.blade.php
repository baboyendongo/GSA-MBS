@extends('layouts.app')

@section('title', 'Instancels | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Instancels')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Pilotage opérationnel</p>
            <h1>Suivi des Instancels</h1>
            <p>Retrouvez et gérez les événements de vos clients.</p>
        </div>
        <button class="primary-button" id="open-instancel-dialog" type="button"><span aria-hidden="true">+</span> Ajouter un InstanceLS</button>
    </div>

    <section class="stats" aria-label="Résumé des Instancels">
        <div class="stat"><span class="stat-label">Total</span><strong class="stat-value">{{ $totalInstancels }}</strong></div>
        <div class="stat"><span class="stat-label">En cours</span><strong class="stat-value">{{ $activeInstancels }}</strong></div>
        <div class="stat"><span class="stat-label">Clôturés</span><strong class="stat-value">{{ $closedInstancels }}</strong></div>
    </section>

    <section class="table-panel" aria-labelledby="instancels-title">
        <div class="table-toolbar">
            <div>
                <h2 class="table-title" id="instancels-title">Tous les Instancels</h2>
                <p class="table-count"><span id="instancel-visible-count">{{ $instancels->total() }}</span> enregistrement(s)</p>
            </div>
            <form class="search-wrap" method="get" action="{{ route('instancels.index') }}">
                <button class="search-icon search-submit" type="submit" aria-label="Rechercher"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.8" cy="8.8" r="5.8" stroke="currentColor" stroke-width="1.7"/><path d="m13.2 13.2 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></button>
                <input class="search-input" id="instancel-search" name="search" type="search" value="{{ request('search') }}" placeholder="Rechercher un client, un événement..." autocomplete="off" aria-label="Rechercher dans les Instancels">
            </form>
        </div>
        <div class="table-scroll">
            <table class="instances-table">
                <thead><tr><th>Date</th><th>Instancel</th><th>Client</th><th>Ligne</th><th>Responsable</th><th>État</th><th>Délais</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($instancels as $instancel)
                        <tr data-instancel-row data-search="{{ mb_strtolower(implode(' ', array_filter([$instancel->date, $instancel->evenement, $instancel->operation, $instancel->ligne, $instancel->codeclient, $instancel->clients, $instancel->ville, $instancel->phase, $instancel->responsable, $instancel->delai, $instancel->date_cloture, $instancel->observation])), 'UTF-8') }}">
                            <td class="date-cell">{{ $instancel->date ? \Illuminate\Support\Carbon::parse($instancel->date)->format('d/m/Y') : '—' }}</td>
                            <td class="event-number">{{ $instancel->evenement }}</td><td class="client-cell">{{ $instancel->clients ?: '—' }}</td><td>{{ $instancel->ligne ?: '—' }}</td>
                            <td>{{ $instancel->responsable ?: '—' }}</td>
                            <td>@if ($instancel->phase)<span class="phase-badge {{ str_contains(mb_strtolower($instancel->phase), 'intégration') ? 'integration' : '' }}">{{ $instancel->phase }}</span>@else<span class="muted-cell">—</span>@endif</td>
                            <td>{{ $instancel->delai ?: '—' }}</td>
                            <td><div class="row-actions"><button class="detail-button" type="button" data-instancel-detail data-instancel-title="{{ $instancel->evenement }}" data-instancel-state="{{ $instancel->phase }}" data-instancel-close-date="{{ $instancel->date_cloture ? \Illuminate\Support\Carbon::parse($instancel->date_cloture)->format('d/m/Y') : '' }}" data-instancel-observation="{{ $instancel->observation }}" title="Afficher les détails" aria-haspopup="dialog" aria-label="Voir les détails de {{ $instancel->evenement }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg></button><button class="table-action-link" type="button" data-instancel-edit data-record-id="{{ $instancel->id }}" data-update-url="{{ route('instancels.update', $instancel->id) }}" data-date="{{ $instancel->date }}" data-evenement="{{ $instancel->evenement }}" data-clients="{{ $instancel->clients }}" data-ligne="{{ $instancel->ligne }}" data-phase="{{ $instancel->phase }}" data-responsable="{{ $instancel->responsable }}" data-delai="{{ $instancel->delai }}" data-date-cloture="{{ $instancel->date_cloture }}" data-observation="{{ $instancel->observation }}" title="Modifier" aria-label="Modifier cet Instancel"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>@if (auth()->user()->is_admin)<button class="danger-button" type="button" data-instancel-delete data-record-name="{{ $instancel->evenement }}" data-delete-url="{{ route('instancels.destroy', $instancel->id) }}" title="Supprimer" aria-label="Supprimer cet Instancel"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="8">Aucun Instancel pour le moment.</td></tr>
                    @endforelse
                    <tr id="instancel-empty-search" hidden><td class="empty-state" colspan="8">Aucun résultat ne correspond à cette recherche.</td></tr>
                </tbody>
            </table>
        </div>
        @if ($instancels->hasPages())
            <div class="pagination-wrap">{{ $instancels->links() }}</div>
        @endif
    </section>

    <dialog class="instance-dialog" id="instancel-dialog" @if ($errors->getBag('default')->any()) data-open="true" @endif>
        <div class="dialog-head"><div><h2>Ajouter un InstanceLS</h2><p>Renseignez les informations de suivi.</p></div><button class="close-button" id="close-instancel-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('instancels.store') }}" data-archive-state-field="phase" data-close-date-field-name="date_cloture">
            @csrf
            <div class="form-grid">
                <div class="form-field"><label for="date">Date *</label><input id="date" name="date" type="date" value="{{ old('date') }}" required autofocus>@error('date')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="evenement">Instancel *</label><input id="evenement" name="evenement" value="{{ old('evenement') }}" required>@error('evenement')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="clients">Client</label><input id="clients" name="clients" value="{{ old('clients') }}"></div>
                <div class="form-field"><label for="ligne">Ligne</label><input id="ligne" name="ligne" value="{{ old('ligne') }}"></div>
                <div class="form-field"><label for="phase">État</label><select id="phase" name="phase" data-archive-state><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}" @selected(old('phase') === $state)>{{ $state }}</option>@endforeach</select>@error('phase')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="responsable">Responsable</label><input id="responsable" name="responsable" value="{{ old('responsable') }}"></div>
                <div class="form-field"><label for="delai">Délais</label><input id="delai" name="delai" type="date" value="{{ old('delai') }}"></div>
                <div class="form-field" data-close-date-field @if (old('phase') !== 'Archivé') hidden @endif><label for="date_cloture">Date de clôture</label><input id="date_cloture" name="date_cloture" type="date" value="{{ old('date_cloture') }}" @disabled(old('phase') !== 'Archivé')>@error('date_cloture')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="observation">Observations</label><textarea id="observation" name="observation">{{ old('observation') }}</textarea></div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instancel-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Ajouter l’Instancel</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="instancel-edit-dialog" @if (session()->has('edit_record_id')) data-open="true" data-record-id="{{ session('edit_record_id') }}" data-old-values="{{ json_encode(session()->getOldInput(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" @endif>
        <div class="dialog-head"><div><h2>Modifier un Instancel</h2><p>Modifiez les informations de suivi.</p></div><button class="close-button" id="close-instancel-edit-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="instancel-edit-form" method="post" action="" data-archive-state-field="phase" data-close-date-field-name="date_cloture">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="edit-instancel-date">Date *</label><input id="edit-instancel-date" name="date" type="date" required>@error('date', 'instancelEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instancel-name">Instancel *</label><input id="edit-instancel-name" name="evenement" required>@error('evenement', 'instancelEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="edit-instancel-client">Client</label><input id="edit-instancel-client" name="clients"></div>
                <div class="form-field"><label for="edit-instancel-line">Ligne</label><input id="edit-instancel-line" name="ligne"></div>
                <div class="form-field"><label for="edit-instancel-state">État</label><select id="edit-instancel-state" name="phase" data-archive-state><option value="">Choisir un état</option>@foreach ($states as $state)<option value="{{ $state }}">{{ $state }}</option>@endforeach</select></div>
                <div class="form-field"><label for="edit-instancel-owner">Responsable</label><input id="edit-instancel-owner" name="responsable"></div>
                <div class="form-field"><label for="edit-instancel-deadline">Délais</label><input id="edit-instancel-deadline" name="delai" type="date"></div>
                <div class="form-field" data-close-date-field hidden><label for="edit-instancel-close-date">Date de clôture</label><input id="edit-instancel-close-date" name="date_cloture" type="date" disabled>@error('date_cloture', 'instancelEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field full"><label for="edit-instancel-observation">Observations</label><textarea id="edit-instancel-observation" name="observation"></textarea>@error('observation', 'instancelEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instancel-edit-dialog" type="button">Annuler</button><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog confirm-dialog" id="instancel-delete-dialog">
        <div class="dialog-head"><div><h2>Supprimer cet Instancel ?</h2><p>Cette action est définitive.</p></div><button class="close-button" id="close-instancel-delete-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="instancel-delete-form" method="post" action="">
            @csrf
            @method('DELETE')
            <p>Confirmez la suppression de <strong id="instancel-delete-name"></strong>.</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-instancel-delete-dialog" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Supprimer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog detail-dialog" id="instancel-detail-dialog" aria-labelledby="instancel-detail-title">
        <div class="dialog-head"><div><h2 id="instancel-detail-title">Détails de l’Instancel</h2><p id="instancel-detail-name"></p></div><button class="close-button" id="close-instancel-detail-dialog" type="button" aria-label="Fermer">×</button></div>
        <div class="detail-content">
            <dl class="detail-list">
                <div><dt>Date clôture</dt><dd id="instancel-detail-close-date">—</dd></div>
                <div><dt>Observations</dt><dd id="instancel-detail-observation">—</dd></div>
            </dl>
        </div>
    </dialog>
@endsection
