<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070809">
    <meta name="description" content="@yield('description', 'Arknight Archive - arsip operator dan kalkulator kebutuhan upgrade fan-made.')">
    <title>@yield('title', 'Arknights Archive')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php($doctor = session('doctor_name'))

    <header class="site-header">
        <div class="shell header-grid">
            <a class="brand" href="{{ route('home') }}" aria-label="Arknights Archive - beranda">
                <span class="brand__mark">AK</span>
                <span class="brand__text">
                    <strong>Arknights Archive</strong>
                    <span>Rhodes Island / Asset Planner</span>
                </span>
            </a>

            <nav class="nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home', 'operators.*') ? 'is-active' : '' }}">
                    @include('partials.icon', ['name' => 'grid'])
                    <span>Operator</span>
                </a>
                <a href="{{ route('planner.show') }}" class="{{ request()->routeIs('planner.*') ? 'is-active' : '' }}">
                    @include('partials.icon', ['name' => 'external'])
                    <span>Planner</span>
                </a>
            </nav>

            <form class="doctor" method="POST" action="{{ route('doctor.store') }}">
                @csrf
                <div class="doctor__field">
                    <label for="doctor_name">Doctor ID</label>
                    <input
                        id="doctor_name"
                        name="doctor_name"
                        type="text"
                        value="{{ old('doctor_name', $doctor) }}"
                        placeholder="guest"
                        maxlength="20"
                        minlength="2"
                        autocomplete="nickname"
                    >
                </div>
                <button class="btn btn--sm" type="submit">{{ $doctor ? 'SYNC' : 'SET ID' }}</button>
            </form>
        </div>
    </header>

    <div class="sysline">
        <div class="shell sysline__inner">
            <span class="sysline__dot" aria-hidden="true"></span>
            <span>DATA LINK <b>ONLINE</b></span>
            <span class="sysline__hide-sm">ROSTER <b>{{ number_format($rosterTotal) }}</b></span>
            <span class="sysline__hide-sm">DOCTOR <b>{{ $doctor ? strtoupper($doctor) : 'GUEST' }}</b></span>
            <span class="sysline__spacer"></span>
            <span class="sysline__hide-sm">REGION <b>EN</b></span>
            <span class="sysline__signal">FAN PROJECT // NOT AFFILIATED</span>
        </div>
    </div>

    <main>
        @if (session('status') || session('error') || $errors->any())
            <div class="shell flash-stack">
                @if (session('status'))
                    <div class="flash flash--ok">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="flash flash--error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="flash flash--error">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="shell site-footer__inner">
            <p>
                <strong>Arknight Archive</strong> adalah proyek fan-made untuk merencanakan upgrade operator.
                Bukan produk resmi Hypergryph atau Studio Montagne. Data operator mengikuti server EN.
            </p>
            <div class="footer-meta">
                <span><i class="dia"></i> {{ number_format($rosterTotal) }} RECORDS</span>
                <span>ASSETS / ACESHIP &amp; YUANYAN</span>
                <span>{{ now()->format('Y') }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
