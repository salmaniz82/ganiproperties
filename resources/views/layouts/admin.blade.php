<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <title>@yield('title', 'Admin') | Gani Property</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $icon = fn (string $name) => match ($name) {
        'home' => '<svg viewBox="0 0 20 20"><path d="M3 9.2 10 3l7 6.2v7.3H5.5V10h9v6.5M8 16.5v-4h4v4"/></svg>',
        'properties' => '<svg viewBox="0 0 20 20"><path d="M3 9.2 10 3l7 6.2v7.3H3zM7 16.5v-5h6v5M5.5 8h9"/></svg>',
        'media' => '<svg viewBox="0 0 20 20"><rect x="3.5" y="4" width="13" height="12" rx="1.5"/><circle cx="7.5" cy="8" r="1.5"/><path d="m5 14 3.5-3 2.5 2 2-1.5 2 2.5"/></svg>',
        'pages' => '<svg viewBox="0 0 20 20"><path d="M5 3.5h7l3 3v10H5zM12 3.5v3h3M7.5 10h5M7.5 13h5"/></svg>',
        'settings' => '<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="3"/><path d="M10 2.5v2m0 11v2m7.5-7.5h-2m-11 0h-2m12.8-5.3-1.4 1.4m-7.8 7.8-1.4 1.4m10.6 0-1.4-1.4M6.1 6.1 4.7 4.7"/></svg>',
        'store' => '<svg viewBox="0 0 20 20"><path d="M3 8h14l-1-4H4zM4.5 8v8h11V8M8 16v-5h4v5"/></svg>',
        default => '',
    };
@endphp

<aside class="sidebar">
    <div class="workspace-switcher">
        <span class="workspace-mark">G</span>
        <div><b>Gani Property</b><small>Property workspace</small></div>
        <span class="chevrons">⌃⌄</span>
    </div>

    <nav class="side-nav">
        <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">{!! $icon('home') !!}<span>Home</span></a>
        <a class="{{ request()->routeIs('admin.properties*') ? 'active' : '' }}" href="{{ route('admin.properties.index') }}">{!! $icon('properties') !!}<span>Properties</span></a>
        <a class="{{ request()->routeIs('admin.media*') ? 'active' : '' }}" href="{{ route('admin.media') }}">{!! $icon('media') !!}<span>Media</span></a>
        <a class="{{ request()->routeIs('admin.pages*') ? 'active' : '' }}" href="{{ route('admin.pages') }}">{!! $icon('pages') !!}<span>Pages</span></a>
        <div class="nav-divider"></div>
        <a class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">{!! $icon('settings') !!}<span>Settings</span></a>
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('home') }}" target="_blank" rel="noopener">{!! $icon('store') !!}<span>View public</span><span class="external">↗</span></a>
        <div class="user-menu">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            <div><b>{{ auth()->user()->name }}</b><small>{{ auth()->user()->email }}</small></div>
            <form method="post" action="{{ route('logout') }}">@csrf<button title="Sign out">•••</button></form>
        </div>
    </div>
</aside>

<main class="admin-main">
    <header class="top-header">
        <button class="icon-button menu-button" type="button">☰</button>
        <div class="breadcrumbs"><span>Gani Property</span><b>/</b><strong>@yield('heading', 'Home')</strong></div>
        <div class="header-actions"><button class="icon-button" title="Search">⌕</button><button class="icon-button" title="Notifications">○</button></div>
    </header>

    <div class="admin-content">
        @if(session('success'))<div class="notice"><span>✓</span>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="notice error"><span>!</span>{{ $errors->first() }}</div>@endif
        @yield('content')
    </div>
</main>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
