@extends('layout')

@section('title', 'Operator Archive | Arknights Archive')
@section('description', 'Arsip seluruh operator Arknights lengkap dengan kelas, rarity, nation, dan kebutuhan promotion.')

@section('content')
    @php
        $query = request()->query();
        $rarityLabels = [6 => '6', 5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1'];
        $hasActiveFilter = filled($filters['q'] ?? null)
            || filled($filters['class'] ?? null)
            || filled($filters['rarity'] ?? null)
            || filled($filters['nation'] ?? null)
            || (($filters['sort'] ?? 'rarity') !== 'rarity');
    @endphp

    <section class="page-head">
        <div class="shell page-head__grid">
            <div>
                <p class="kicker">Rhodes Island // Operator Database</p>
                <h1 class="page-title">Operator <em>Archive</em></h1>
                <p class="page-sub">
                    Semua operator yang tersedia di server EN, dari reservist sampai elite operation.
                    Cari berdasarkan nama, kelas, rarity, atau nation lalu buka lembar datanya.
                </p>
            </div>

            <dl class="head-stats" aria-label="Statistik arsip">
                <div class="head-stat">
                    <dt>Records</dt>
                    <dd>{{ number_format($stats['total']) }}</dd>
                </div>
                <div class="head-stat">
                    <dt>Six Star</dt>
                    <dd>{{ number_format($stats['sixStar']) }}</dd>
                </div>
                <div class="head-stat">
                    <dt>Classes</dt>
                    <dd>{{ $stats['classes'] }}</dd>
                </div>
                <div class="head-stat">
                    <dt>Nations</dt>
                    <dd>{{ $stats['nations'] }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <div class="shell">
        <form method="GET" action="{{ route('home') }}" class="search-block">
            @if (filled($filters['class'] ?? null))
                <input type="hidden" name="class" value="{{ $filters['class'] }}">
            @endif

            @if (filled($filters['rarity'] ?? null))
                <input type="hidden" name="rarity" value="{{ $filters['rarity'] }}">
            @endif

            <div class="searchbar">
                <label class="searchbar__inner" for="q">
                    @include('partials.icon', ['name' => 'search'])
                    <span class="sr-only">Cari operator</span>
                    <input
                        id="q"
                        name="q"
                        type="search"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Cari nama, subclass, nation, atau nomor arsip..."
                        autocomplete="off"
                    >
                </label>

                <button type="submit">
                    <span>Search</span>
                    @include('partials.icon', ['name' => 'arrow'])
                </button>
            </div>

            <div class="filterbar">
                <div class="class-tabs" aria-label="Filter kelas">
                    <a
                        class="class-tab {{ blank($filters['class'] ?? null) ? 'is-active' : '' }}"
                        href="{{ route('home', \Illuminate\Support\Arr::except($query, ['class', 'page'])) }}"
                    >
                        All
                        <span class="mono">{{ number_format($stats['total']) }}</span>
                    </a>

                    @foreach (\App\Models\Operator::classes() as $class)
                        <a
                            class="class-tab {{ ($filters['class'] ?? null) === $class ? 'is-active' : '' }}"
                            href="{{ route('home', array_merge(\Illuminate\Support\Arr::except($query, ['class', 'page']), ['class' => $class])) }}"
                        >
                            @include('partials.class-icon', ['class' => $class])
                            {{ $class }}
                            <span class="mono">{{ $classCounts[$class] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="filterbar">
                <div class="rarity-tabs" aria-label="Filter rarity">
                    <a
                        class="rarity-tab {{ blank($filters['rarity'] ?? null) ? 'is-active' : '' }}"
                        href="{{ route('home', \Illuminate\Support\Arr::except($query, ['rarity', 'page'])) }}"
                        style="--rarity: var(--text)"
                    >
                        ALL
                    </a>

                    @foreach ($rarityLabels as $rarity => $label)
                        <a
                            class="rarity-tab {{ (string) ($filters['rarity'] ?? '') === (string) $rarity ? 'is-active' : '' }}"
                            href="{{ route('home', array_merge(\Illuminate\Support\Arr::except($query, ['rarity', 'page']), ['rarity' => $rarity])) }}"
                            style="--rarity: var(--r{{ $rarity }})"
                            aria-label="Rarity {{ $rarity }}"
                        >
                            <i class="dia"></i> {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="filter-selects">
                    <label class="select-wrap">
                        <span>Nation</span>
                        <select name="nation" data-auto-submit>
                            <option value="">Semua</option>
                            @foreach ($nations as $nation)
                                <option value="{{ $nation }}" @selected(($filters['nation'] ?? null) === $nation)>{{ $nation }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="select-wrap">
                        <span>Sort</span>
                        <select name="sort" data-auto-submit>
                            <option value="rarity" @selected(($filters['sort'] ?? 'rarity') === 'rarity')>Rarity</option>
                            <option value="name" @selected(($filters['sort'] ?? 'rarity') === 'name')>Name A-Z</option>
                            <option value="class" @selected(($filters['sort'] ?? 'rarity') === 'class')>Class</option>
                        </select>
                    </label>

                    <button type="submit" class="btn btn--sm">Filter</button>
                </div>
            </div>
        </form>

        @if ($hasActiveFilter)
            <div class="active-filters">
                <span>Active</span>

                @if (filled($filters['q'] ?? null))
                    <span class="chip-filter">
                        Q: {{ $filters['q'] }}
                        <a href="{{ route('home', \Illuminate\Support\Arr::except($query, ['q', 'page'])) }}" aria-label="Hapus filter pencarian">&times;</a>
                    </span>
                @endif

                @if (filled($filters['class'] ?? null))
                    <span class="chip-filter">
                        {{ $filters['class'] }}
                        <a href="{{ route('home', \Illuminate\Support\Arr::except($query, ['class', 'page'])) }}" aria-label="Hapus filter kelas">&times;</a>
                    </span>
                @endif

                @if (filled($filters['rarity'] ?? null))
                    <span class="chip-filter">
                        Rarity {{ $filters['rarity'] }}
                        <a href="{{ route('home', \Illuminate\Support\Arr::except($query, ['rarity', 'page'])) }}" aria-label="Hapus filter rarity">&times;</a>
                    </span>
                @endif

                @if (filled($filters['nation'] ?? null))
                    <span class="chip-filter">
                        {{ $filters['nation'] }}
                        <a href="{{ route('home', \Illuminate\Support\Arr::except($query, ['nation', 'page'])) }}" aria-label="Hapus filter nation">&times;</a>
                    </span>
                @endif

                @if (($filters['sort'] ?? 'rarity') !== 'rarity')
                    <span class="chip-filter">
                        Sort: {{ $filters['sort'] }}
                        <a href="{{ route('home', \Illuminate\Support\Arr::except($query, ['sort', 'page'])) }}" aria-label="Reset pengurutan">&times;</a>
                    </span>
                @endif

                <a class="chip-filter" href="{{ route('home') }}">Reset all</a>
            </div>
        @endif

        <div class="result-line">
            <span>
                Records found
                <b>{{ number_format($operators->total()) }}</b>
                @if (filled($filters['q'] ?? null))
                    for &ldquo;{{ $filters['q'] }}&rdquo;
                @endif
            </span>
            <span>Page <b>{{ str_pad($operators->currentPage(), 2, '0', STR_PAD_LEFT) }}</b> / {{ str_pad($operators->lastPage(), 2, '0', STR_PAD_LEFT) }}</span>
        </div>

        @if ($operators->isEmpty())
            <div class="empty-state">
                <p class="empty-state__code">Error 404 // No record</p>
                <h2>Tidak ada operator yang cocok</h2>
                <p>Coba hapus sebagian filter atau gunakan kata kunci lain.</p>
                <a class="btn btn--solid" href="{{ route('home') }}">Reset pencarian</a>
            </div>
        @else
            <div class="op-grid">
                @foreach ($operators as $operator)
                    @include('partials.operator-card', ['operator' => $operator, 'favoriteIds' => $favoriteIds])
                @endforeach
            </div>

            @include('partials.pagination', ['paginator' => $operators])
        @endif
    </div>
@endsection
