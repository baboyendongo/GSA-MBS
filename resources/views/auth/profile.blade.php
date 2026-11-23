@extends('layouts.app')

@section('title', 'Profil | Gestion et suivi des activités MBS')
@section('breadcrumb', 'Profil')

@section('content')
    <div class="page-heading">
        <div><p class="eyebrow">Mon compte</p><h1>Profil</h1><p>Consultez vos informations et modifiez votre mot de passe.</p></div>
    </div>

    <div class="profile-grid">
        <section class="table-panel profile-info" aria-labelledby="profile-info-title">
            <div class="table-toolbar">
                <div><h2 class="table-title" id="profile-info-title">Informations personnelles</h2></div>
            </div>
            <dl class="detail-list">
                <div><dt>Nom</dt><dd>{{ $user->name }}</dd></div>
                <div><dt>Adresse e-mail</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>Rôle</dt><dd>{{ $user->is_admin ? 'Administrateur' : 'Utilisateur' }}</dd></div>
            </dl>
        </section>

        <section class="table-panel profile-password" aria-labelledby="profile-password-title">
            <div class="table-toolbar">
                <div><h2 class="table-title" id="profile-password-title">Changer le mot de passe</h2><p class="table-count">Minimum 8 caractères.</p></div>
            </div>
            <form class="profile-password-form" method="post" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="form-field">
                    <label for="profile-current-password">Mot de passe actuel *</label>
                    <input id="profile-current-password" name="current_password" type="password" autocomplete="current-password" required>
                    @error('current_password', 'passwordChange')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-field">
                    <label for="profile-new-password">Nouveau mot de passe * (8 caractères minimum)</label>
                    <input id="profile-new-password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                    @error('password', 'passwordChange')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-field">
                    <label for="profile-password-confirmation">Confirmer le nouveau mot de passe *</label>
                    <input id="profile-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                </div>
                <div class="dialog-actions"><button class="primary-button" type="submit">Modifier le mot de passe</button></div>
            </form>
        </section>
    </div>
@endsection
