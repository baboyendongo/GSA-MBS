<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion et suivi des activités MBS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" aria-label="Navigation principale">
            <a class="brand" href="{{ route('dashboard') }}"><img class="brand-logo" src="{{ asset('logo_fr.png') }}" alt="Mauritel"><span class="brand-name">Gestion et suivi des activités MBS</span></a>
            <div class="nav-label">Espace de travail</div>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">⌂</span><span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('instancels.*') ? 'active' : '' }}" href="{{ route('instancels.index') }}" @if (request()->routeIs('instancels.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">▦</span><span class="nav-text">Instancels</span>
            </a>
            <a class="nav-link {{ request()->routeIs('instances.*') ? 'active' : '' }}" href="{{ route('instances.index') }}" @if (request()->routeIs('instances.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">◫</span><span class="nav-text">Instance</span>
            </a>
            <a class="nav-link {{ request()->routeIs('projects-b2b.*') ? 'active' : '' }}" href="{{ route('projects-b2b.index') }}" @if (request()->routeIs('projects-b2b.*')) aria-current="page" @endif>
                <span class="nav-icon" aria-hidden="true">◫</span><span class="nav-text">Project B2B</span>
            </a>
            <div class="sidebar-bottom">
                @if (auth()->user()->is_admin)
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.index') }}" @if (request()->routeIs('admin.*')) aria-current="page" @endif>
                        <span class="nav-icon" aria-hidden="true">⚙</span><span class="nav-text">Administration</span>
                    </a>
                @endif
                <div class="sidebar-foot">Mauritel · MBS</div>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <a class="mobile-brand" href="{{ route('dashboard') }}"><img class="mobile-brand-logo" src="{{ asset('logo_fr.png') }}" alt="Mauritel"><span class="mobile-brand-name">Gestion et suivi des activités MBS</span></a>
                <div class="crumb">Espace de travail <span aria-hidden="true">/</span> @yield('breadcrumb', 'Dashboard')</div>
                <div class="profile">
                    <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <button class="profile-menu-button" id="profile-menu-button" type="button" aria-haspopup="menu" aria-expanded="false">
                        <span class="profile-name">{{ auth()->user()->name }}</span>
                        <span aria-hidden="true">▾</span>
                    </button>
                    <div class="profile-menu" id="profile-menu" role="menu" hidden>
                        <a class="profile-menu-link" href="{{ route('profile') }}" role="menuitem">Profil</a>
                        <button class="profile-menu-link" id="open-logout-dialog" type="button" role="menuitem">Déconnexion</button>
                    </div>
                </div>
            </header>
            <section class="content">
                @if (session('status'))
                    <div class="flash-message" role="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </section>
        </main>
    </div>

    <dialog class="instance-dialog confirm-dialog" id="logout-dialog">
        <div class="dialog-head"><div><h2>Se déconnecter ?</h2><p>Vous devrez vous reconnecter pour accéder à l’application.</p></div><button class="close-button" id="close-logout-dialog" type="button" aria-label="Fermer">×</button></div>
        <form class="dialog-form" method="post" action="{{ route('logout') }}">
            @csrf
            <p>Confirmez-vous la déconnexion de votre compte ?</p>
            <div class="dialog-actions"><button class="secondary-button" id="cancel-logout-dialog" type="button">Annuler</button><button class="danger-confirm-button" type="submit">Se déconnecter</button></div>
        </form>
    </dialog>
</body>
</html>
