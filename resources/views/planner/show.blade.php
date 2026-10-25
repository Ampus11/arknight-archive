@extends('layout')

@section('title', 'Asset Planner | Arknights Archive')
@section('description', 'Hitung EXP, LMD, battle records, dan material promotion yang dibutuhkan untuk upgrade operator Arknights.')

@section('content')
    @php
        $maxElite = $selected
            ? max(array_keys(config('arknights.max_levels.'.$selected->rarity)))
            : 2;
    @endphp

    <section class="page-head">
        <div class="shell page-head__grid">
            <div>
                <p class="kicker">Rhodes Island // Resource Estimate</p>
                <h1 class="page-title">Asset <em>Planner</em></h1>
                <p class="page-sub">
                    Tentukan posisi operator saat ini dan target akhirnya. Kalkulator akan menjumlahkan EXP,
                    LMD, battle records, dan material promotion yang masih kurang dari stokmu.
                </p>
            </div>

            <dl class="head-stats" aria-label="Cara kerja planner">
                <div class="head-stat">
                    <dt>Step 1</dt>
                    <dd>Select</dd>
                </div>
                <div class="head-stat">
                    <dt>Step 2</dt>
                    <dd>Range</dd>
                </div>
                <div class="head-stat">
                    <dt>Step 3</dt>
                    <dd>Stock</dd>
                </div>
            </dl>
        </div>
    </section>

    <div class="shell">
        <div class="planner-head">
            <span>Planner terminal / EN data</span>
            <span>
                @if ($selected)
                    {{ $selected->name }} &middot; R{{ $selected->rarity }} {{ $selected->class }}
                @else
                    No operator selected
                @endif
            </span>
        </div>

        <div class="planner-shell">
            <form class="panel planner-form" method="GET" action="{{ route('planner.show') }}">
                <header class="panel__head">
                    <div class="panel__title-group">
                        <span class="panel__index">Input / 03 sections</span>
                        <h2 class="panel__title">Upgrade Parameters</h2>
                    </div>
                    <span class="panel__hint">Semua biaya dalam EN</span>
                </header>

                <div class="panel__body">
                    <section class="form-section">
                        <header class="form-section__head">
                            <h3 class="form-section__title">01 / Operator</h3>
                            <span class="form-section__index">{{ number_format($operators->count()) }} records</span>
                        </header>

                        <div class="form-section__body">
                            <div class="op-picker">
                                <label class="op-picker__search" for="operator-search">
                                    @include('partials.icon', ['name' => 'search'])
                                    <span class="sr-only">Filter daftar operator</span>
                                    <input
                                        id="operator-search"
                                        type="search"
                                        placeholder="Filter daftar: nama, kelas, nation..."
                                        autocomplete="off"
                                        data-operator-search
                                    >
                                </label>

                                <select class="input" name="operator" id="operator-select" required>
                                    <option value="">Pilih operator...</option>

                                    @foreach (\App\Models\Operator::classes() as $class)
                                        @php($group = $operators->where('class', $class))

                                        @if ($group->isNotEmpty())
                                            <optgroup label="{{ strtoupper($class) }} / {{ $group->count() }} OPERATORS">
                                                @foreach ($group as $operator)
                                                    <option
                                                        value="{{ $operator->slug }}"
                                                        data-search="{{ mb_strtolower($operator->name.' '.$operator->class.' '.$operator->subclass.' '.$operator->nation) }}"
                                                        @selected(($form['operator'] ?? null) === $operator->slug)
                                                    >
                                                        {{ $operator->name }} // R{{ $operator->rarity }} {{ $operator->subclass }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>

                                <p class="op-picker__hint">Gunakan kolom filter untuk mempersempit daftar tanpa mengubah pilihan.</p>
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <header class="form-section__head">
                            <h3 class="form-section__title">02 / Level Range</h3>
                            <span class="form-section__index">E0 &rarr; E2</span>
                        </header>

                        <div class="form-section__body">
                            <div class="field-row">
                                <label class="field">
                                    <span>Elite Saat Ini</span>
                                    <select class="input" name="from_elite">
                                        @foreach ([0, 1, 2] as $elite)
                                            <option
                                                value="{{ $elite }}"
                                                @selected((int) ($form['from_elite'] ?? 0) === $elite)
                                                @disabled($elite > $maxElite)
                                            >E{{ $elite }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="field">
                                    <span>Level Saat Ini</span>
                                    <input
                                        class="input"
                                        type="number"
                                        name="from_level"
                                        min="1"
                                        max="90"
                                        value="{{ $form['from_level'] ?? 1 }}"
                                    >
                                </label>

                                <label class="field">
                                    <span>Target Elite</span>
                                    <select class="input" name="to_elite">
                                        @foreach ([0, 1, 2] as $elite)
                                            <option
                                                value="{{ $elite }}"
                                                @selected((int) ($form['to_elite'] ?? 0) === $elite)
                                                @disabled($elite > $maxElite)
                                            >E{{ $elite }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="field">
                                    <span>Target Level</span>
                                    <input
                                        class="input"
                                        type="number"
                                        name="to_level"
                                        min="1"
                                        max="90"
                                        value="{{ $form['to_level'] ?? 1 }}"
                                    >
                                </label>
                            </div>
                        </div>
                    </section>

                    <section class="form-section">
                        <header class="form-section__head">
                            <h3 class="form-section__title">03 / Current Stock</h3>
                            <span class="form-section__index">Optional</span>
                        </header>

                        <div class="form-section__body">
                            <label class="field">
                                <span>LMD Tersedia</span>
                                <input
                                    class="input"
                                    type="number"
                                    name="owned_lmd"
                                    min="0"
                                    step="1"
                                    value="{{ $ownedLmd }}"
                                >
                            </label>

                            <div class="stock-grid">
                                @foreach ($materials as $material)
                                    @php($stock = $owned[$material->id] ?? null)

                                    <label class="stock-row" style="--tier-color: {{ $material->color }}">
                                        <span class="stock-row__name">
                                            <span class="tier">T{{ $material->tier }}</span>
                                            <span>{{ $material->name }}</span>
                                        </span>
                                        <input
                                            type="number"
                                            name="owned[{{ $material->id }}]"
                                            min="0"
                                            inputmode="numeric"
                                            placeholder="0"
                                            value="{{ $stock === null ? '' : $stock }}"
                                            aria-label="Stok {{ $material->name }}"
                                        >
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <div class="detail-actions">
                        <button class="btn btn--solid" type="submit">
                            Hitung Kebutuhan
                            @include('partials.icon', ['name' => 'arrow'])
                        </button>
                        <a class="btn" href="{{ route('planner.show') }}">Reset</a>
                    </div>
                </div>
            </form>

            <aside class="planner-side">
                <section class="panel">
                    <header class="panel__head">
                        <div class="panel__title-group">
                            <span class="panel__index">Output / Estimate</span>
                            <h2 class="panel__title">Result</h2>
                        </div>
                        @if ($ownedLmd > 0)
                            <span class="panel__hint">LMD stock {{ number_format($ownedLmd) }}</span>
                        @endif
                    </header>

                    @if ($selected === null)
                        <div class="result-empty">
                            <p class="result-empty__code">Awaiting input</p>
                            <h3>Pilih operator dulu</h3>
                            <p>Isi form di samping, lalu tekan hitung untuk melihat kebutuhan EXP, LMD, dan material.</p>
                        </div>
                    @else
                        <div class="panel__body">
                            <div class="selected-op">
                                <img src="{{ $selected->avatarUrl() }}" alt="{{ $selected->name }}">
                                <div>
                                    <p class="selected-op__name">{{ $selected->name }}</p>
                                    <p class="selected-op__meta">
                                        R{{ $selected->rarity }} &middot; {{ $selected->class }} &middot; {{ $selected->subclass }}
                                    </p>
                                    <p class="selected-op__meta">{{ $selected->nation }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($result !== null && ! $result['ok'])
                            <div class="panel__body">
                                <div class="result-error">{{ $result['error'] }}</div>
                            </div>
                        @elseif ($result !== null)
                            <div class="stat-grid">
                                <div class="stat">
                                    <dt>Total EXP</dt>
                                    <dd>{{ number_format($result['exp']) }}</dd>
                                </div>
                                <div class="stat">
                                    <dt>LMD Total</dt>
                                    <dd>{{ number_format($result['lmd_total']) }}</dd>
                                </div>
                                <div class="stat {{ $result['lmd_missing'] > 0 ? 'stat--warn' : 'stat--ok' }}">
                                    <dt>LMD Kurang</dt>
                                    <dd>{{ number_format($result['lmd_missing']) }}</dd>
                                </div>
                                <div class="stat">
                                    <dt>Promotion</dt>
                                    <dd>{{ $result['promotions'] === [] ? '—' : implode(' / ', array_map(fn ($p) => 'E'.$p, $result['promotions'])) }}</dd>
                                </div>
                            </div>

                            <div class="result-block">
                                <h3 class="result-block__title">Cost Breakdown</h3>
                                <ul class="result-list">
                                    <li>
                                        <span class="label">LMD leveling</span>
                                        <span class="value">{{ number_format($result['lmd_leveling']) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">LMD promotion</span>
                                        <span class="value">{{ number_format($result['lmd_promotion']) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">LMD dimiliki</span>
                                        <span class="value">{{ number_format($result['lmd_owned']) }}</span>
                                    </li>
                                    <li>
                                        <span class="label">Sisa LMD</span>
                                        <span class="value {{ $result['lmd_missing'] > 0 ? 'text-warn' : 'text-ok' }}">
                                            {{ number_format($result['lmd_missing']) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            @if ($result['promotions'] !== [])
                                <div class="result-block">
                                    <h3 class="result-block__title">Material Promotion</h3>

                                    @if ($result['materials'] === [])
                                        <ul class="result-list">
                                            <li><span class="label">Tidak ada material tambahan.</span></li>
                                        </ul>
                                    @else
                                        <ul class="need-list">
                                            @foreach ($result['materials'] as $material)
                                                @php($percent = $material['need'] > 0 ? min(100, round($material['have'] / $material['need'] * 100)) : 100)

                                                <li class="need" style="--tier-color: {{ $material['color'] }}">
                                                    <span class="need__name">{{ $material['name'] }}</span>
                                                    <span class="need__meta">
                                                        {{ $material['have'] }} / {{ $material['need'] }}
                                                        @if ($material['missing'] > 0)
                                                            <strong class="text-warn">&minus;{{ $material['missing'] }}</strong>
                                                        @endif
                                                    </span>
                                                    <span class="need__bar" aria-hidden="true">
                                                        <i class="{{ $material['missing'] > 0 ? 'is-missing' : '' }}" style="width: {{ $percent }}%"></i>
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif

                            <div class="result-block">
                                <h3 class="result-block__title">Battle Records</h3>
                                <ul class="result-list">
                                    @foreach ($result['records'] as $record)
                                        <li>
                                            <span class="label">{{ $record['name'] }}</span>
                                            <span class="value">&times;{{ $record['count'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif
                </section>
            </aside>
        </div>
    </div>
@endsection
