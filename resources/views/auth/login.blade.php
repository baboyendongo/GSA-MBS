@extends('layouts.guest')

@section('title', 'Connexion | Mauritel MBS')

@section('content')
    <main class="auth-page">
        <section class="auth-side" aria-label="Mauritel MBS">
            <a class="auth-brand" href="{{ route('login') }}">
                <span class="auth-brand-logo-shell"><img class="auth-brand-logo" src="{{ asset('logo_fr.png') }}" alt="Mauritel"></span>
                <span class="auth-brand-copy"><span class="auth-brand-tagline">Gestion et suivi des activités MBS</span><span class="auth-brand-meta">PORTAIL MBS</span></span>
            </a>
            <div class="auth-side-main">
                <p class="auth-kicker">ESPACE PROFESSIONNEL</p>
                <h1>Vos activités MBS,<br>en un espace organisé.</h1>
                <p class="auth-side-copy">Un accès simple et sécurisé à votre espace de suivi.</p>
            </div>
            <div class="auth-side-foot"><span class="auth-security-mark" aria-hidden="true"></span> Connexion sécurisée <span class="auth-side-divider">/</span> Mauritel</div>
        </section>

        <section class="auth-main" aria-labelledby="login-title">
            <div class="auth-panel">
                <p class="auth-kicker">ACCÈS AU PORTAIL</p>
                <h2 id="login-title">Connexion</h2>
                <p class="auth-intro">Utilisez vos identifiants professionnels pour continuer.</p>

                @if (session('status'))
                    <div class="flash-message" role="status">{{ session('status') }}</div>
                @endif

                <form class="auth-form" method="post" action="{{ route('login.store') }}">
                    @csrf
                    <div class="auth-field">
                        <label for="email">Adresse e-mail</label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" placeholder="nom@entreprise.mr" required autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" aria-describedby="email-error">
                        </div>
                        @error('email')<p class="auth-error" id="email-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="auth-field">
                        <label for="password">Mot de passe</label>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 1 1 8 0v3"/><path d="M12 14v3"/></svg>
                            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Votre mot de passe" required aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" aria-describedby="password-error">
                        </div>
                        @error('password')<p class="auth-error" id="password-error">{{ $message }}</p>@enderror
                    </div>
                    <label class="auth-remember" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                        <span>Se souvenir de moi</span>
                    </label>
                    <button class="primary-button auth-submit" type="submit">Se connecter</button>
                </form>
            </div>
            <p class="auth-main-foot">© {{ date('Y') }} Mauritel · MBS</p>
        </section>
    </main>
@endsection
