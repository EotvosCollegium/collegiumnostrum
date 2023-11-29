<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom title -->
    <title>
        @if (View::hasSection('title'))
            @yield('title') |
        @endif
        {{ config('app.name', 'Collegium Nostrum') }}
    </title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @if (config('app.debug', 'false'))
    <style>
            .top-bar {background-color: #dd0000;}
    </style>
    @endif
    <link rel="icon" type="image/x-icon" href="https://eotvos.elte.hu/media/da/30/e9a7dd3606d1cf1b44a674b6b3f4d7b52638a8a0cdc56526c14c0010e4c0/ec_logo.png">
</head>
<body>
    <div id="app">
    <div class="col-xs-12 banner-container hidden-md">

        <a href="/">
            <img class="header-banner-image" src="/images/eotvoscoll-nagy.svg" alt="ugrás a főoldalra"><img src="https://eotvos.elte.hu/media/da/30/e9a7dd3606d1cf1b44a674b6b3f4d7b52638a8a0cdc56526c14c0010e4c0/ec_logo.png" class="header-banner-image">
        </div>
        <nav class="navbar navbar-expand-md navbar-light shadow-sm top-bar">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Collegium Nostrum') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">{{ __('Kezdőlap') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('alumni.index') }}">{{ __('Alumni adatbázis') }}</a>
                        </li>
                        <!-- Logout, if logged in (otherwise the login option is at the bottom of the page so that it won't be confusing for guests) -->
                        @auth
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Kijelentkezés') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <footer class="mb-4 footer-wrapper">
            <div class="container">
                <div class="d-flex flex-column align-items-center">
                    <img class="nka-acknowledgement" alt="Nemzeti Kulturális Alap" src="/images/nka_2023(2).png" />
                    <i class="nka-acknowledgement">A projekt a Nemzeti Kulturális Alap támogatásával valósult meg.</i>

                    <hr class="nka-acknowledgement">

                    <div>
                        <span class="small">Collegium Nostrum</span>
                        <span class="mx-1">·</span>
                        <span class="small">Laravel {{ app()->version() }}</span>
                        <span class="mx-1">·</span>
                        <span class="small">PHP {{ phpversion() }}</span>
                    </div>

                    <div>
                        <span class="small"><a href="https://eotvos.elte.hu/" class="footer-link">Eötvös József Collegium</a></span>
                    </div>

                    <!-- Login -->
                    @guest
                    <div>
                        <a class="small footer-link" href="{{ route('login') }}">{{ __('Bejelentkezés adminisztrátoroknak') }}</a>
                    </div>
                    @endguest
                </div>
            </div>
        </footer>

        @yield('scripts')
    </div>
</body>
</html>
