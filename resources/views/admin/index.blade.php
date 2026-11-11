@extends('layouts.app')

@section('title', 'Administration | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Administration')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Espace sécurisé</p><h1>Administration</h1><p>Comptes utilisateurs et vue d’ensemble des activités.</p></div>
        <button class="primary-button" id="open-admin-create-user" type="button"><span aria-hidden="true">+</span> Ajouter un utilisateur</button>
    </div>

    @if ($errors->getBag('default')->any())
        <div class="flash-message" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="stats admin-stats" aria-label="Indicateurs d’administration">
        <div class="stat"><span class="stat-label">Utilisateurs</span><strong class="stat-value">{{ $totalUsers }}</strong></div>
        <div class="stat"><span class="stat-label">Administrateurs</span><strong class="stat-value">{{ $totalAdmins }}</strong></div>
        <div class="stat"><span class="stat-label">Instancels</span><strong class="stat-value">{{ $totalInstancels }}</strong></div>
        <div class="stat"><span class="stat-label">Instances</span><strong class="stat-value">{{ $totalInstances }}</strong></div>
        <div class="stat"><span class="stat-label">Projets B2B</span><strong class="stat-value">{{ $totalProjects }}</strong></div>
    </section>

    <section class="table-panel admin-states-panel" aria-labelledby="admin-states-title">
        <div class="table-toolbar">
            <div><h2 class="table-title" id="admin-states-title">États des formulaires</h2><p class="table-count">{{ $states->total() }} état(s) configuré(s).</p></div>
            <button class="primary-button" id="open-admin-create-state" type="button"><span aria-hidden="true">+</span> Ajouter un état</button>
        </div>
        <div class="table-scroll">
            <table class="instances-table">
                <thead><tr><th>Suivi</th><th>État</th><th>Disponibilité</th><th>Utilisation</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($states as $state)
                        <tr>
                            <td>{{ $stateTypes[$state->tracking_type]['label'] ?? $state->tracking_type }}</td>
                            <td class="client-cell">{{ $state->label }}</td>
                            <td><span class="admin-role {{ $state->is_active ? 'is-admin' : '' }}">{{ $state->is_active ? 'Actif' : 'Désactivé' }}</span></td>
                            <td>{{ $stateUsage[$state->id] }} fiche(s)</td>
                            <td><div class="row-actions">
                                <button class="table-action-link" type="button" data-admin-state-edit data-record-id="{{ $state->id }}" data-update-url="{{ route('admin.states.update', $state) }}" data-state-label="{{ $state->label }}" title="Renommer" aria-label="Renommer l’état {{ $state->label }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>
                                <form method="post" action="{{ route('admin.states.toggle', $state) }}">@csrf @method('PATCH')<button class="state-toggle-button" type="submit" @disabled($state->is_active && $stateUsage[$state->id] > 0) title="{{ $state->is_active ? 'Désactiver' : 'Activer' }}" aria-label="{{ $state->is_active ? 'Désactiver' : 'Activer' }} l’état {{ $state->label }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2v10"/><path d="M7 4.8a9 9 0 1 0 10 0"/></svg></button></form>
                                @if ($stateUsage[$state->id] === 0)
                                    <button class="danger-button" type="button" data-admin-state-delete data-record-name="{{ $state->label }}" data-delete-url="{{ route('admin.states.destroy', $state) }}" title="Supprimer" aria-label="Supprimer l’état {{ $state->label }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="5">Aucun état configuré. Lancez le seeder des états ou ajoutez-en un.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($states->hasPages())
            <div class="pagination-wrap">{{ $states->links() }}</div>
        @endif
    </section>

    <nav class="admin-links" aria-label="Gestion des suivis">
        <a class="secondary-button" href="{{ route('instancels.index') }}">Instancels</a>
        <a class="secondary-button" href="{{ route('instances.index') }}">Instances</a>
        <a class="secondary-button" href="{{ route('projects-b2b.index') }}">Project B2B</a>
    </nav>

    <section class="table-panel" aria-labelledby="admin-users-title">
        <div class="table-toolbar">
            <div><h2 class="table-title" id="admin-users-title">Comptes utilisateurs</h2><p class="table-count">{{ $users->total() }} compte(s)</p></div>
        </div>
        <div class="table-scroll">
            <table class="instances-table">
                <thead><tr><th>Nom</th><th>Adresse e-mail</th><th>Rôle</th><th>Créé le</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="client-cell">{{ $user->name }} @if ($user->is(auth()->user()))<span class="admin-user-self">(vous)</span>@endif</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="admin-role {{ $user->is_admin ? 'is-admin' : '' }}">{{ $user->is_admin ? 'Administrateur' : 'Utilisateur' }}</span></td>
                            <td class="date-cell">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                            <td><div class="row-actions"><button class="table-action-link" type="button" data-admin-user-edit data-record-id="{{ $user->id }}" data-update-url="{{ route('admin.users.update', $user) }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-is-admin="{{ $user->is_admin ? '1' : '0' }}" title="Modifier" aria-label="Modifier le compte de {{ $user->name }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>@unless ($user->is(auth()->user()))<button class="danger-button" type="button" data-admin-user-delete data-record-name="{{ $user->name }}" data-delete-url="{{ route('admin.users.destroy', $user) }}" title="Supprimer" aria-label="Supprimer le compte de {{ $user->name }}"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg></button>@endunless</div></td>
                        </tr>
                    @empty
                        <tr><td class="empty-state" colspan="5">Aucun compte utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="pagination-wrap">{{ $users->links() }}</div>
        @endif
    </section>

    <dialog class="instance-dialog" id="admin-create-state-dialog" @if (session()->has('open_state_create')) data-open="true" @endif>
        <div class="dialog-head"><div><h2>Ajouter un état</h2><p>Choisissez le suivi auquel cet état appartient.</p></div><button class="close-button" id="close-admin-create-state" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('admin.states.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-field"><label for="admin-state-type">Suivi *</label><select id="admin-state-type" name="tracking_type" required><option value="" disabled @selected(old('tracking_type') === null)>Choisir un suivi</option>@foreach ($stateTypes as $type => $definition)<option value="{{ $type }}" @selected(old('tracking_type') === $type)>{{ $definition['label'] }}</option>@endforeach</select>@error('tracking_type', 'stateCreate')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-state-label">Nom de l’état *</label><input id="admin-state-label" name="label" value="{{ old('label') }}" required>@error('label', 'stateCreate')<div class="field-error">{{ $message }}</div>@enderror</div>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-create-state" type="button">Annuler</button><button class="primary-button" type="submit">Ajouter l’état</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="admin-state-edit-dialog" @if (session()->has('edit_state_id')) data-open="true" data-record-id="{{ session('edit_state_id') }}" data-old-values="{{ json_encode(session()->getOldInput(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" @endif>
        <div class="dialog-head"><div><h2>Renommer un état</h2><p>Les fiches existantes seront mises à jour avec le nouveau nom.</p></div><button class="close-button" id="close-admin-state-edit" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="admin-state-edit-form" method="post" action="">
            @csrf
            @method('PUT')
            <div class="form-field"><label for="admin-edit-state-label">Nom de l’état *</label><input id="admin-edit-state-label" name="label" required>@error('label', 'stateEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-state-edit" type="button">Annuler</button><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog confirm-dialog" id="admin-state-delete-dialog">
        <div class="dialog-head"><div><h2>Supprimer cet état ?</h2><p>Cette action est définitive.</p></div><button class="close-button" id="close-admin-state-delete" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="admin-state-delete-form" method="post" action="">
            @csrf
            @method('DELETE')
            <p>Confirmez la suppression de l’état <strong id="admin-state-delete-name"></strong>.</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-state-delete" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Supprimer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="admin-create-user-dialog" @if (session()->has('open_admin_create')) data-open="true" @endif>
        <div class="dialog-head"><div><h2>Ajouter un utilisateur</h2><p>Créez un accès à Gestion et suivi des activités MBS.</p></div><button class="close-button" id="close-admin-create-user" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-field"><label for="admin-create-name">Nom *</label><input id="admin-create-name" name="name" value="{{ old('name') }}" required>@error('name')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-create-email">Adresse e-mail *</label><input id="admin-create-email" name="email" type="email" value="{{ old('email') }}" required>@error('email')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-create-password">Mot de passe * (12 caractères minimum)</label><input id="admin-create-password" name="password" type="password" autocomplete="new-password" required>@error('password')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-create-password-confirmation">Confirmer le mot de passe *</label><input id="admin-create-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
                <label class="auth-remember form-field full" for="admin-create-is-admin"><input id="admin-create-is-admin" name="is_admin" type="checkbox" value="1" @checked(old('is_admin'))><span>Accorder les droits administrateur</span></label>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-create-user" type="button">Annuler</button><button class="primary-button" type="submit">Créer le compte</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog" id="admin-user-edit-dialog" @if (session()->has('edit_user_id')) data-open="true" data-record-id="{{ session('edit_user_id') }}" data-old-values="{{ json_encode(session()->getOldInput(), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}" @endif>
        <div class="dialog-head"><div><h2>Modifier un utilisateur</h2><p>Laissez le mot de passe vide pour le conserver.</p></div><button class="close-button" id="close-admin-user-edit" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="admin-user-edit-form" method="post" action="">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-field"><label for="admin-edit-name">Nom *</label><input id="admin-edit-name" name="name" required>@error('name', 'userEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-edit-email">Adresse e-mail *</label><input id="admin-edit-email" name="email" type="email" required>@error('email', 'userEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-edit-password">Nouveau mot de passe</label><input id="admin-edit-password" name="password" type="password" autocomplete="new-password">@error('password', 'userEdit')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="form-field"><label for="admin-edit-password-confirmation">Confirmer le nouveau mot de passe</label><input id="admin-edit-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password"></div>
                <label class="auth-remember form-field full" for="admin-edit-is-admin"><input id="admin-edit-is-admin" name="is_admin" type="checkbox" value="1">@error('is_admin', 'userEdit')<span class="field-error">{{ $message }}</span>@enderror<span>Administrateur</span></label>
            </div>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-user-edit" type="button">Annuler</button><button class="primary-button" type="submit">Enregistrer</button></div>
        </form>
    </dialog>

    <dialog class="instance-dialog confirm-dialog" id="admin-user-delete-dialog">
        <div class="dialog-head"><div><h2>Supprimer ce compte ?</h2><p>Cette action est définitive.</p></div><button class="close-button" id="close-admin-user-delete" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" id="admin-user-delete-form" method="post" action="">
            @csrf
            @method('DELETE')
            <p>Confirmez la suppression du compte <strong id="admin-user-delete-name"></strong>.</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-admin-user-delete" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Supprimer</button></div>
        </form>
    </dialog>
@endsection
